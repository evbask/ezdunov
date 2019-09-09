<?php

namespace App\Components;

use \Exception;
use App\LogsSms;
use App\Qtsms\QtsmsClass;

/**
 * классы для работы с смс
 * @todo необходимо реализовать фабрику для выбора провайдера
 * @todo JIorD 2019/02/04 порефакторить getErrorsText, jobOfSend
 */
class Sms
{
    private $QtOptions = [
        'user' => '27874',
        'pass' => '100maslo',
        'host' => 'go.qtelecom.ru'
    ];

    /** имя отправителя */
    public $sender = "dispetcher";

    /**
     * код ошибки
     */
    private $errors = [];

    protected $qtErrors = [
        // ошибки которые возращаются в ответе
        -20117 => 'Неправильный номер телефона.',
        -20170 => 'Слишком большой текст сообщения. Максимальная длина не должна превышать 160 байт.',
        -20171 => 'Не пройдена проверка текста сообщения на наличие недопустимых слов и/или фраз.',
        -20158 => 'Отправитель или получатель в черном списке.',
        -20167 => 'Сработало ограничение по отправке одинакового текста на один и тот же номер в течение небольшого промежутка времени. Обратитесь в поддержку, если хотите отключить или уменьшить период.',
        -20144 => 'Нет доступного тарифа для запрашиваемого направления.',
        -20147 => 'Нет подходящего тарифа у вышестоящего контрагента.',
        -20174 => 'Политика маршрутизации не найдена.',
        -20154 => 'Ошибка транспорта. При возникновении этой ошибки обратитесь в службу поддержки.',
        -20148 => 'Неподдерживаемое направление.',
        -20163 => 'Неразрешенный отправитель',
        -20135 => 'Не достаточно средств на счете',
        // ошибки которые не возращаются, но их тоже ловим
        -1     => 'Невозможно соединиться с сервером.',
        -2     => 'Непредвиденная ошибка',
    ];

    /**
     * провайдер для отправки смс (сейчас только один)
     * @var QtsmsClass
     */
    protected $providerSms;

    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * получить текстовое представление ошибки
     * @return string
     */
    public function getErrorsText(int $code)
    {
        if (array_key_exists($code, $this->qtErrors)) {
            return $this->qtErrors[$code];
        } else {
            return "Не определенно.";
        }
    }

    public function __construct()
    {
        extract($this->QtOptions, EXTR_OVERWRITE);
        $this->providerSms = new QtsmsClass($user, $pass, $host);
    }

    /**
     * отправить смс
     * @param $body текст смс
     * @param $phone телефон
     * @return bool
     */

    public function send($body, $phone)
    {
        $this->response = $this->providerSms->post_message($body, $phone, $this->sender, uniqid('verif'), 600);
        $success = $this->checkResponse();

        LogsSms::create([
            'sms_target'    =>  $phone,
            'sms_text'      =>  $body,
            'sms_sender'    =>  $this->sender,
            'sms_status'    =>  $success ? LogsSms::S_SUCCESS : $this->errors[0],
        ]);
        
        return $success;
    }
    /**
     * проверка ответа сервера на ошибки
     * ошибка может быть как строкой, так и внутри xml
     * @param string $response
     * @return bool
     */
    protected function checkResponse()
    {
        libxml_use_internal_errors(true); 
        $xml = simplexml_load_string($this->response);
        //если ответ xml
        if ($xml) { 
            if ($xml->errors ?? false) {
                $this->errors[] = (int)$xml->errors->error['code'];
            }
        } else {
            if ($this->response == 'Невозможно соединиться с сервером.') {
                $this->errors[] = -1;
            } else {
                $this->errors[] = -2;
            }
        }

        return empty($this->errors) ? true : false;
    }

    /**
     * отправка смс из очереди
     * @throws Exception
     * @return bool
     */
    public function jobOfSend(LogsSms $sms)
    {
        $this->response = $this->providerSms->post_message($sms->sms_text, $sms->sms_target, $sms->sms_sender, uniqid('verif'), 600);
        if ($this->checkResponse()) {
            $sms->sms_status = LogsSms::S_SUCCESS;
            $sms->save();
            return true;
        } else {
            $sms->sms_status = $this->errors[0];
            $sms->save();
            // вывод сообщения ошибки в консоль
            echo $this->getErrorsText($this->errors[0]) . ' Код ошибки:' . $this->errors[0] . PHP_EOL;
            throw new Exception($this->getErrorsText($this->errors[0]) . ' Код ошибки:' . $this->errors[0], $this->errors[0]);
        }
    }
}
