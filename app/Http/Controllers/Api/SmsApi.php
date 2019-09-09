<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use \App\Http\Controllers\Controller;
use \App\Qtsms\QtsmsClass;
use \App\SmsVerification;

use \Illuminate\Database\Eloquent\ModelNotFoundException;

function generateRandomString($length = 10) {
    // $characters = '0123456789';
    // $charactersLength = strlen($characters);
    // $randomString = '';
    // for ($i = 0; $i < $length; $i++) {
    //     $randomString .= $characters[rand(0, $charactersLength - 1)];
    // }
    // return $randomString;
    return str_pad(rand(1, 99999), $length, 0, STR_PAD_LEFT);
}

/**
 * Контроллер для работы с sms через api
 */
class SmsApi extends Controller {
    
    /** Опции для подключения к Qtelecom серверу */
    private $QtOptions = [
        'user' => '27874',
        'pass' => '100maslo',
        'host' => 'go.qtelecom.ru'
    ];

    /**
     * Возвращает балланс аккаунта Qtelecom
     *
     * @param Request $request
     * @return void
     */
    public function getBalance(Request $request){
        $Qt_user = $this->QtOptions['user'];
        $Qt_pass = $this->QtOptions['pass'];
        $Qt_host = $this->QtOptions['host'];
        
        $QtSms = new QtsmsClass($Qt_user, $Qt_pass, $Qt_host);
        
        $balance = $QtSms->get_balance();
        
        return json_encode($balance);
    }

    /**
     * Посылаем код для верификации
     *
     * @param Request $request
     * @return void
     */
    public function sendVerification(Request $request){
        $max_sent_period = config('sms_verify.max_sent_period');
        $user = $request->user();
        $phone = str_replace(['(',')','-','_'], '', $user->phone);
        try{
            $verification = SmsVerification::where('phone', $phone)
                            ->where('user_id', $user->id)
                            ->where('done', false)
                            ->where('sent_time', '>', time()-$max_sent_period)
                            ->first();
        } catch (ModelNotFoundException $e){
            unset($verification);
        }

        $response = [];
        if(isset($verification)){
            $response['result'] = 'early';
            return json_encode($response);
        }

        $Qt_user = $this->QtOptions['user'];
        $Qt_pass = $this->QtOptions['pass'];
        $Qt_host = $this->QtOptions['host'];
        
        $QtSms = new QtsmsClass($Qt_user, $Qt_pass, $Qt_host);
        $sms_code = generateRandomString(5);
        $period=600;
        $sms_id = uniqid('verif');
        SmsVerification::create([
            'user_id' => $user->id,
            'phone' => $phone,
            'sms_code' => $sms_code,
        ]);
        $result=$QtSms->post_message($sms_code, $phone, 'dispetcher',$sms_id,$period);
        $response['result'] = $result;
        return json_encode($response);
    }
}