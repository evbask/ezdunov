<?php
/**
 * @link https://lib.rfibank.ru/display/publicdocs
 */
namespace App\Components;

use Exception;

use App\Logs_rfi_bank;
use App\User;
use App\LogsSession;
use App\UserCard;

use App\Components\Toolkit;

class RfiBank
{
    const BASE_URL = 'https://partner.rficb.ru/';
    const PAYMENT_URL = 'alba/input/'; /** Путь к оплате */
    const REFUND_URL = 'alba/refund/'; /** Путь к возврату средств */
    const RECURRENT_CANCEL_URL = 'alba/recurrent_change/'; /** Путь к отмене рекуррентного платежа */

    const SECRET_KEY = '5663c75c1324dcf75e959d11abb14cdf'; /** Секретный ключ */

    /** ID сервиса на сайте */
    const SERVICE_ID = 81384;
    //const SERVICE_ID = 77367;           // для тестов
    /** тип платежа */
     //const PAYMENT_TYPE = 'spg';
    const PAYMENT_TYPE = 'spg_test';    // для тестов

    const BANK_IP = [
        '195.245.72.1',
        '195.245.72.40',
        '185.222.52.1'
    ];

    public static function isRightIp()
    {
        return in_array(Toolkit::getRealIp(), self::BANK_IP);
    }

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    /**
     * @todo JIorD переписать
     */
    public function createLog()
    {
        $log = new Logs_rfi_bank();
        $log->user_id = $this->params['user_id'];
        $log->status = Logs_rfi_bank::STATUS_NOT_FINISHED;
        $log->summ = $this->params['cost'];
        $log->service_id = $this->params['service_id'];
        $log->phone = $this->params['phone_number'];
        $log->email = $this->params['email'];
		$log->type = $this->params['payment_type'];
        $log->recurrent_type = isset($this->params['recurrent_type']) ? $this->params['recurrent_type'] : null;
        $log->for_check_online_activation = $this->params['checkOnlineActivation'] ?? null;
        $log->for_bind_card_to_service = $this->params['bindingCardToService'] ?? null;

		return $log->save() ? $log : null;
    }

    /**
     * @brief Построение запроса RFC 3986
     * @param array $queryData параметры запроса
     * @param string $argSeparator разделитель
     * @return string
     */
    protected function _http_build_query_rfc_3986($queryData, $argSeparator = '&')
    {
        $r = '';
        $queryData = (array)$queryData;
        if (!empty($queryData)) {
            foreach ($queryData as $k => $queryVar) {
                $r .= $argSeparator;
                $r .= $k;
                $r .= '=';
                $r .= rawurlencode($queryVar);
            }
        }
        return trim($r, $argSeparator);
    }

    /**
     * @brief Формирование подписи по всем полям HTTP запроса
     * @param string $method метод: GET, POST, PUT, DELETE
     * @param string $url URL запроса без параметров
     * @param array $params параметры GET и POST
     * @param string $secretKey секретный ключ
     * @param bool $skipPort если в url нестандартный порт, участвует ли он в подписи
     * @return string
     */
    protected function sign($method, $url, $params, $secretKey, $skipPort = false)
    {
        ksort($params, SORT_LOCALE_STRING);

        $urlParsed = parse_url($url);
        $path = $urlParsed['path'];
        $host = isset($urlParsed['host']) ? $urlParsed['host'] : "";

        if (isset($urlParsed['port']) && $urlParsed['port'] != 80) {
            if (!$skipPort) {
                $host .= ":{$urlParsed['port']}";
            }
        }

        $method = strtoupper($method) == 'POST' ? 'POST' : 'GET';

        $data = implode("\n",
            array(
                $method,
                $host,
                $path,
                $this->_http_build_query_rfc_3986($params)
            )
        );

        $signature = base64_encode(
            hash_hmac("sha256",
                "{$data}",
                "{$secretKey}",
                TRUE
            )
        );

        return $signature;
    }

    protected function _curl($url, $post = false)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 45);

        if ($post) {
            $query = http_build_query($post);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        }

        $result = curl_exec($ch);

        if ($result === false){
            return ['status' => 'error', 'message' => 'Ошибка при подключении к удалённому серверу'];
        }

        curl_close($ch);
        return json_decode($result, true);
    }

    /**
     * Проверка поля check
     * @param string $fields
     * @return bool
     */
    protected function checkByFields($fields = '')
    {
        $data = array_map(function ($fieldName) {
            return $this->params[$fieldName];
        }, explode(',', $fields));

        return md5(join('', array_values($data)) . static::SECRET_KEY) === $this->params['check'];
    }

    /**
     * Подпись для пополнения счета
     * @return string
     * @throws Exception
     */
    public function signPayment($url='')
    {
        if (!$this->params)
            throw new Exception('Не заполнены параметры');

       	if ($url == '') {
			$url = static::BASE_URL . static::PAYMENT_URL;
		}

        $check = $this->sign('POST', $url, $this->params, static::SECRET_KEY);

        return $check;
    }

    /**
     * Проверка, верный ли пришёл ответ об оплате
     * @return bool
     */
    public function checkPaymentSign()
    {
        $fields = 'tid,name,comment,partner_id,service_id,order_id,' .
            'order_id,type,partner_income,system_income,test';

        return $this->checkByFields($fields);
    }

    /**
     * Проверка, верный ли пришёл ответ.
     * Ответ содержит доп. параметры
     * @param string $url
     * @return bool
     */

    /*public function checkFullPaymentSign($url = ''){
        $params = array(
            'tid' => $this->params['tid'],
            'name' => $this->params['name'],
            'comment' => $this->params['comment'],
            'partner_id' => $this->params['partner_id'],
            'service_id' => $this->params['service_id'],
            'order_id' => $this->params['order_id'],
            'type' => $this->params['type'],
            'partner_income' => $this->params['partner_income'],
            'system_income' => $this->params['system_income']
        );

        $params['check'] = md5(join('', array_values($params)) . static::SECRET_KEY);
        $check = $this->params['check'];
        $result = $params['check'];
        LogsSession::newLog(['params'=> $params, 'this_params'=>$this->params],"has - '$check' and sign '$result'");
        return $params['check'] === $this->params['check'];
    }*/

    public function checkFullPaymentSign($url = '')
    {
        $check = $this->params['check'];
        unset($this->params['check']);
        $result = $this->sign('POST', $url, $this->params, self::SECRET_KEY);
        //LogsSession::newLog(['params'=> $_POST],"has - '$check' and sign '$result'");
        return $result === $check;
    }

    public function refund()
    {
        $url = self::BASE_URL . self::REFUND_URL;
        $this->params['check'] = $this->sign('POST', $url, $this->params, self::SECRET_KEY);

        $result = $this->_curl($url, $this->params);

        return $result;
    }

    /**
     * Рекуррентное списание
     * @param object $user пользователь
     * @param int $cost сумма списания
     * @param string $card конкретная карта
     * @return array
     */
	public function recurrentPayment(User $user, $cost = null, $card = null)
	{
		if (!$user->recurrent_pay_status) {
			return ['status' => 'error', 'msg' => 'Пользователь не подключен к рекуррентным платежам'];
		}

		$this->params = [
			'cost' => $cost,
			'email' => $user->email,
			'phone_number' => $user->phone,
			'user_id' => $user->id,
			'service_id' => self::SERVICE_ID,
			'version' => '2.0',
			'payment_type' => self::PAYMENT_TYPE,
			'background' => '1',
			'recurrent_type' => 'next',
		];

		if (isset($card)) {
            $pays = UserCard::cardRecurrent($user->id, $card);
			if (!$pays) {
				return ['status' => 'error', 'msg' => 'Карта не подключена к рекуррентным платежам'];
			}
		} else {
            $pays = UserCard::cardRecurrent($user->id);
            // $pays = $user->payCards;
			if (!$pays) {
				return ['status' => 'error', 'msg' => 'Пользователь не подключен к рекуррентным платежам'];
            }
        }

		$activeRecurrentPays = [];
		foreach ($pays as $onePay) {
			if (isset($onePay->card) && isset($onePay->recurrent_order_id)) {
				$activeRecurrentPays[] = $onePay->recurrent_order_id;
			}
		}

        if (empty($activeRecurrentPays)) {
            return ['status' => 'error', 'msg' => 'У пользователя нет карт с подключенными рекуррентными платежами'];
        } else {
            foreach ($activeRecurrentPays as $try) {
                $log = $this->createLog();
                if (!$log) {
                    continue;
                }
                $this->params['recurrent_order_id'] = $try;
                $this->params['order_id'] = $log->id;
                $this->params['name'] = 'Пополнение баланса Ezdunov.ru №' . $log->id;
                $this->params['check'] = $this->signPayment();

                $result = $this->_curl(self::BASE_URL . self::PAYMENT_URL, $this->params);
                // РФИ по разном присылает ответ в случае ошибки msg/message
                if (!array_key_exists('msg', $result)) {
                    $result['msg'] = $result['message'];
                }

                // сохраним результат в случае ошибки, в случае успеха придет POST в PaymentController
                if ($result['status'] != 'success') {
                    $log->status = Logs_rfi_bank::STATUS_ERROR;
                    $log->result_str = $result['msg'];
                    $log->updated = date("Y-m-d H:i:s");
                    $log->save();
                }

                // Если списываем вручную с конкретной карты, сразу получаем ответ
                if (isset($card)) {
                    if ($result['status'] != 'success') {
                        if($result['msg'] == 'Otkaz bez ob\'jasnenija prichin'){
                            $userCard = UserCard::searchCard($user->id, $card);
                            $userCard->cardRecurrentCancel();

                            $this->cancelRecurrentPayments($user,$card);
                            $result['msg'] = 'Отказ без объяснения причин';
                        }
                    }
                    return $result;
                }
                // Если автоматически, то цикл пробует пока не получит success
                if ($result['status'] == 'success') {
                    return $result;
                } else {
                    if ($result['msg'] == 'Nedostatochno sredstv') {
                        $user->prev_recurrent_try_sum = $this->params['cost'];
                        $user->save('prev_recurrent_try_sum');
                    }
                }
            }
            return ['status' => 'error', 'msg' => 'Не удалось пополнить баланс'];
        }
	}

    /**
     * @brief Отключение рекуррентных платежей для пользователя
     * @param object $user пользователь
     * @param string $card конкретная карта
     * @return array
     */
	public function cancelRecurrentPayments(User $user, $card = null)
	{
		if (!$user->checkRecurrentPayStatus()) {
			return ['status' => 'error', 'msg' => 'Пользователь не подключен к рекуррентным платежам'];
        }

		$url = self::BASE_URL . self::RECURRENT_CANCEL_URL;
		$this->params = [
			'operation' => 'cancel',
			'service_id' => self::SERVICE_ID,
			'version' => '2.0',
        ];

        $userCard = UserCard::searchCard($user->id, $card);

        if (!$userCard && !isset($userCard->recurrent_order_id)) {
            return ['status' => 'error', 'msg' => 'Карта не подключена к рекуррентным платежам'];
        }
        else
        {
            $this->params['order_id'] = $userCard->recurrent_order_id;
            $this->params['check'] = $this->signPayment($url);
            $result = $this->_curl($url, $this->params);
            if ($result['status'] === "success") {
                /**
                 * Отмена рекурентных платежей происходит в контроллере PaymentController->actionFullNotificationRfiBank()
                 */

                $logArr = [];
                $logArr['user'] = $user->id;
                $logArr['card'] = $card;
                $logArr['recurrent_order_id'] = $userCard->recurrent_order_id;
                $logArr['result'] = $result;
                $log = new Log('cancel_recurrent_pays');
                $log->info('Администратор '.Yii::app()->user->model->fio.' ('.Yii::app()->user->model->id.') отключал карту пользователя', $logArr);

                if (!$user->checkRecurrentPayStatus()) {
                        $user->recurrent_pay_status = 0;
                        $user->saveAttributes(['recurrent_pay_status']);
                }
                return $result;
            } else {
                if (!array_key_exists('msg', $result)) {
                    $result['msg'] = $result['message'];
                    unset($result['message']);
                }
                return $result;
            }
        }
    }
}
