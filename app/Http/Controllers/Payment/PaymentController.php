<?php
namespace App\Http\Controllers\Payment;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use App\User;
use App\LogsSession;
use App\Logs_rfi_bank;
use App\UserCard;
use App\LogsBalance;
use App\Page;

use App\Components\RfiBank;
use App\Components\Toolkit;

use App\Http\Controllers\Controller;

/**
 * контроллер отвечает за работу с рфи банком
 * @todo 2019/01/20 JIorD взято из другого проекта, требуется рефакторинг
 */
class PaymentController extends Controller
{
    public function actionValidateRfiLog(Request $request)
    {
        header("Access-Control-Allow-Origin: *");

        //echo "ok";
        //return;
        $post = $_POST;

        $card_on_rec_pays = false;
        $checkOnlineActivation = false;
        $bindingCardToService = false;
        if (isset($post['checkOnlineActivation']) && $post['checkOnlineActivation']) {
            $checkOnlineActivation = true;
        }
        if (isset($post['bindingCardToService']) && $post['bindingCardToService']){
            $bindingCardToService = true;
        }
        if (isset($post['card']) && $post['card']) {
            /**
             * Если карта уже привязана к рекуррентным платежам,
             * то исключаем параметры для РП из запроса к РФИ,
             * то есть отправляем пользователя на обычное пополнение (без галочки на согласие повторных списаний)
             * */
             if ($post['card_on_rec_pays']) {
                 $card_on_rec_pays = true;
                 unset($post['recurrent_type']);
                 unset($post['recurrent_comment']);
                 unset($post['recurrent_url']);
                 unset($post['recurrent_period']);
             }
        }
        //Убираем вспомогательные параметры, чтобы они не участвовали в формировании подписи
        unset($post['card']);
        unset($post['_token']);
        unset($post['card_on_rec_pays']);
        $post['checkOnlineActivation'] = $checkOnlineActivation;
        $post['bindingCardToService']  = $bindingCardToService;
        $bank = new RfiBank($post);
        $log = $bank->createLog();
        if (!$log) {
            return json_encode(['success' => false, 'message' => 'Ошибка при пополнении баланса']);
        }

        if ($checkOnlineActivation) {
            $bank->params['name'] = 'check-online-activation';
        } else {
            $bank->params['name'] = 'Пополнение баланса Ezdunov.ru №' . $log->id;
        }
        if ($bindingCardToService) {
            $bank->params['name'] = 'binding-card-to-service';
        } else {
            $bank->params['name'] = 'Пополнение баланса Ezdunov.ru №' . $log->id;
        }
        $bank->params['order_id'] = $log->id;
        unset($bank->params['check']);
        unset($bank->params['checkOnlineActivation']);
        unset($bank->params['bindingCardToService']);

        $r = [
            'success' => true,
            'order_id' => $log->id,
            'name' => $bank->params['name'],
            'check' => $bank->signPayment(),
            //'card_on_rec_pays' => $card_on_rec_pays,
            //'card_binding_id' => $bank->params['card_binding_id']
        ];
        return  json_encode($r);

    }

    public function actionRecurrentPayment(Request $request){
        $user = Auth::user();
        if (!$user || !$user->checkGroup('staff')) {
            return responce('Доступ запрещен', 403);
        }

        $id = $request->user_id;
        $user = User::find($id);
        $cost = $request->cost;
        $card = $request->card;

        $bank = new RfiBank();
        try {
            $result = $bank->recurrentPayment($user, $cost, $card);
            if ($result['status'] == 'success') {
                return json_encode($result);
            } else {
                return json_encode(['status' => $result['status'], 'msg' => $result['msg']]);
            }
        } catch (Exception $e) {
            return  json_encode(['status' => 'error', 'msg' => 'Произошла непредвиденная ошибка!']);
        }
    }

    public function actionCancelRecurrentPayment(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->checkGroup('staff')) {
            return responce('Доступ запрещен', 403);
        }

        $id = Yii::app()->request->getParam('user_id');
        $card = Yii::app()->request->getParam('card');

        $user = User::model()->findByPk($id);

        $bank = new RfiBank();
        $result = $bank->cancelRecurrentPayments($user, $card);

        if ($result['status'] == 'success') {
            Yii::app()->user->setFlash('success', 'Карта пользователя ' . '<a href="/admin/user/edit/' . $user->id . '">' . $user->fio . '</a>' . ' успешно отключена от рекуррентных списаний');
        }
        echo CJSON::encode($result);
    }

    public function actionFullNotificationRfiBank(Request $request)
    {
	    Log::info('FullNotificationRfiBank.', $_POST);
        //$logModel = new Log(Log::RFI_BANK);
        //$logModel->info('Получен запрос от RFI-банка', $_POST);
        //return response(200, "ok");
        if (!RfiBank::isRightIp()) {
            return response('Ваш IP-адрес не входит в список разрешённых', 403);
        }

        $post = $_POST;

        $currentUrl = $request->fullUrl();
            // $session_log = LogsSession::create([
            //     'session_id' => session()->getId(),
            //     'log_data' => json_encode(['post'=> $_POST, 'get' => $_GET]),
            //     'log_message' => "$currentUrl"
            // ]);
            // $session_log->save();
        $bank = new RfiBank($post);

        if ($bank->checkFullPaymentSign($currentUrl)) {
            LogsSession::newLog(['post' => $_POST],"Вернаяя подпись");
            // $session_log = LogsSession::create([
            //     'session_id' => session()->getId(),
            //     //'log_data' => json_encode(['post'=> $_POST, 'get' => $_GET]),
            //     'log_message' => 'ПОдпись верна'
            // ]);
            // $session_log->save();
            $sum = (int)$post['system_income'];
            $log = Logs_rfi_bank::find($post['order_id']);
            if ($log->summ == $sum && $log->status == Logs_rfi_bank::STATUS_NOT_FINISHED) {
                if ($post['command'] == 'success') {
                    DB::beginTransaction();
                    try {
                        $log->transaction_id = $post['tid'];
                        $log->partner_id = $post['partner_id'];
                        $log->partner_income = $post['partner_income'];
                        $log->type = $post['type'];
                        $log->status = Logs_rfi_bank::STATUS_FINISHED;
                        $log->card   = isset($post['card']) ? $post['card'] : null;
                        $log->cardholder = isset($post['cardholder']) ? $post['cardholder'] : null;
                        $log->result_str = $post['resultStr'];
                        $log->card_binding_id = isset($post['card_binding_id']) ? $post['card_binding_id'] : null;

                        if ($log->recurrent_type == 'first' && isset($post['recurrent_order_id'])) {

                            $firstRecPays = Logs_rfi_bank::where('card', $post['card'])
                                ->where('user_id', $log->user_id)
                                ->where('recurrent_type', 'first')
                                ->whereNotNull('recurrent_order_id')
                                ->whereIn('status', [Logs_rfi_bank::STATUS_FINISHED, Logs_rfi_bank::STATUS_PARTIALRETURN, Logs_rfi_bank::STATUS_FULLRETURN])
                                ->get();

							//если с этой карты уже есть "первый" рекурретный платеж
                        	if ($firstRecPays) {
								$log->recurrent_order_id = null;
							} else {
								$log->recurrent_order_id = $post['recurrent_order_id'];
							}
						} elseif ($log->recurrent_type == 'first' && !isset($post['recurrent_order_id'])) {
							//если пользователь снял галочку
							$log->recurrent_order_id = null;
							$log->agreement_rejected = true;
						} elseif (isset($post['recurrent_order_id'])) {
							$log->recurrent_order_id = $post['recurrent_order_id'];
						} else {
							$log->recurrent_order_id = null;
						}
                        $log->save();

                        // добавляем карту в список карт пользователей
                        $recurrent_order_id = isset($post['recurrent_order_id']) ? $post['recurrent_order_id'] : null;

                        $checkCard = UserCard::searchCard($log->user_id, $log->card);
                        if ($checkCard ?? false) {
                            LogsSession::newLog(['post' => $checkCard],"card");
                            Log::info('logs summ.', ['sum' => $log->summ]);
                            $checkCard->summ = $checkCard->summ + $sum;
                            
                            if(!empty($recurrent_order_id))
                            $checkCard->recurrent_order_id = $recurrent_order_id;
                            $checkCard->update();
                        }
                        else
                        {
                            $card_data = [
                                'user_id' => $log->user_id,
                                'card' => $log->card,
                                'cardholder' => $log->cardholder,
                                'summ' => $log->summ,
                                'card_binding_id' => $log->card_binding_id,
                                'recurrent_order_id' => $recurrent_order_id,
                            ];
                            $checkCard = UserCard::create($card_data);
                            $checkCard->save();
                        }

                        $user = User::find($log->user_id);

                        if ($log->recurrent_type == 'next') {
                            $user->balanceIncrease($log->summ, LogsBalance::T_BALANCEPAYMENT_REC);
                        } else {
                            $user->balanceIncrease($log->summ, LogsBalance::T_BALANCEPAYMENT);
                        }

                        $user->max_sum_withdrawal = $user->maxSumWithdrawal();
                        if (isset($post['recurrent_order_id']) && $log->recurrent_type == 'first' && !$user->recurrent_pay_status) {
                            $user->recurrent_pay_status = true;
                        }

                        $user->save();

                        // Ловим жуликов, пока что тут массив заполянем вручную
                        // $badUsers = $this->getBadUsersCards();
                        // if (in_array($log->card, array_keys($badUsers))
                        //     || (
                        //         in_array($log->cardholder, $badUsers)
                        //         && !in_array($log->cardholder, ['', '-', 'MOMENTUM R', 'MEGAFON CLIENT', 'QVC CARD', 'YAMONEY VIRTUAL'])
                        //     )
                        // ) {
                        //     Tickets::addWithAnswer(
                        //         User::getSystemRobotId(),
                        //         null,
                        //         'Пополнение с карты жулика',
                        //         0,
                        //         'Менеджеры',
                        //         'Произошло пополнение баланса с карты предполагаемого жулика. '
                        //         . "\n" . $log->card . ' ' .  $log->cardholder . "\n"
                        //         . 'Возможно забаненный пользователь снова зарегистрировался. '
                        //         . "\n" . '<a href="/admin/user/edit/' . $user->id . '">(' . $user->id . ')' . $user->fio . ' ' . $user->phone . '</a>',
                        //         Tickets::SJ_CHECKNEEDED
                        //     );
                        // }

                        DB::commit();

						if ($log->for_bind_card_to_service) {
                            if ($log->sendReturnPaymentRequest()) {
                                $user->balanceDecrease($log->summ, LogsBalance::T_CASHOUT_REQUEST);
                            }
                        }
                    } catch (Exception $e) {
                        //$logModel->error('Exception при подтверждении платежа', $post);
                        DB::rollback();
                    }
                } elseif ($post['command'] == 'cancel'){
                    $log->status = Logs_rfi_bank::STATUS_ERROR;
                    $log->result_str = $post['resultStr'];
                    $log->save();
                    $session_log->log_message = $session_log->log_message . "Ответ банка содержит код ошибки\n";
                    $session_log->save();
                }
            }

			if ($post['command'] == 'recurrent_cancel' || $post['command'] == 'recurrent_expire') {
				$parentPay = Logs_rfi_bank::find($post['recurrent_order_id']);
				$parentPay->recurrent_order_id = null;
				if ($post['command'] == 'recurrent_expire') {
					$parentPay->recurrent_type = 'expired';
				}
                $parentPay->save();

                $userCard = UserCard::searchCardByRecurrentId($post['recurrent_order_id']);
                if ($userCard) {
                    $userCard->cardRecurrentCancel();
                }

				$user = User::find($parentPay->user_id);
				if (!$user->checkRecurrentPayStatus()) {
					$user->recurrent_pay_status = 0;
					$user->save();
				}
			}
        } else {
            $session_log = LogsSession::create([
                'session_id' => session()->getId(),
                //'log_data' => json_encode(['post'=> $_POST, 'get' => $_GET]),
                'log_message' => 'Подпись не верна'
            ]);
            $session_log->save();

        }
    }

    // Страница успешной покупки РФИ банка, сюда кидает юзера после оплаты
    public function actionRfiSuccessPayment()
    {
        if (strpos($_SERVER['HTTP_REFERER'], "partner.rficb.") === false) {
            return response('Forbidden', 403);
        }

        $user = User::where('phone', $_GET['phone_number'])->first();
        if ($user) {
            return view('success_payment', ['page_title' => 'title', 'page_description' => 'description', 'page_keywords' => 'keywords']);
        }
        return view('success_payment', ['page_title' => 'title', 'page_description' => 'description', 'page_keywords' => 'keywords']);
    }

    // Ловим жуликов, пока что тут массив заполянем вручную
    public function getBadUsersCards()
    {
        return [
            '427630XXXXXX7336' => 'EGOR KOSTOMAROV',
            '427638XXXXXX9380' => 'OKSANA FISENKO',
            '481776XXXXXX1317' => 'KIRSANOVA IRINA',
            '481776XXXXXX7817' => 'MOMENTUM R',
            '546911XXXXXX5292' => 'VIKTORIA REZNIK',
            '427638XXXXXX0996' => 'MARIA SARSIKEEVA',
            '533669XXXXXX6192' => 'EVGENIY GRITSENKO',
            '510621XXXXXX2972' => 'NIKOLAY VOLKOV',
            '533669XXXXXX2134' => 'MOMENTUM R',
            '533669XXXXXX6278' => 'MADINA SAFAROVA',
            '481776XXXXXX3391' => 'ATABEK ERGASHOV',
            '546960XXXXXX6439' => 'HAMZAD HAXJIMAGAMADOV',
            '546940XXXXXX2259' => 'IZATILLA KARABAEV',
            '546940XXXXXX2259' => 'IZATILLA KARABAEB',
            '539714XXXXXX2609' => '',
            '545293XXXXXX9531' => 'MEGAFON CLIENT',
            '481776XXXXXX3329' => 'MOMENTUM R',
            '481776XXXXXX5355' => 'MOMENTUM R',
            '481776XXXXXX2942' => 'MOMENTUM R',
            '481776XXXXXX9536' => 'MOMENTUM R',
            '481776XXXXXX9580' => 'MOMENTUM R',
            '404266XXXXXX2686' => 'ANDREEV ARTUR',
            '489049XXXXXX4945' => 'QVC CARD',
            '481776XXXXXX5809' => 'ANDREEV ARTUR',
            '489049XXXXXX7485' => '-',
            '489049XXXXXX9198' => '-',
            '481776XXXXXX5809' => '-',
            '489049XXXXXX2503' => 'ANDREEV ARTUR',
            '489049XXXXXX0878' => 'ANDREEV ARTUR',
            '489049XXXXXX0878' => '-',
            '427638XXXXXX6240' => 'MAXIMZEMLEZIN',
            '530331XXXXXX8931' => '-',
            '639002XXXXXXXX5350' => '-',
            '427660XXXXXX8109' => '-',
            '427901XXXXXX6383' => '-',
            '639002XXXXXXXX4291' => '-',
            '481776XXXXXX0017' => '-',
            '555949XXXXXX4970' => '-',
            '481776XXXXXX4779' => '-',
            '510621XXXXXX9517' => '-',
            '510621XXXXXX9739' => 'YAMONEY VIRTUAL',
            '510621XXXXXX4733' => '-',
            '481776XXXXXX2179' => '-',
            '489049XXXXXX4320' => '-'
        ];
    }
}
