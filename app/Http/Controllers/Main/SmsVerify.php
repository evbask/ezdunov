<?php

namespace App\Http\Controllers\Main;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User as User;
use \App\SmsVerification;

use Illuminate\Database\Eloquent\ModelNotFoundException as ModelNotFoundException; 


class SmsVerify extends Controller{
    public function checkSmsCode(Request $request){
        $sms_code = $request->sms_code;
        $user = $request->user();
        $max_sent_period = config('sms_verify.max_sent_period');
        try {
            $verification = SmsVerification::where('phone', str_replace(['(',')','-','_'], '',$user->phone))
                                           ->where('user_id', $user->id)
                                           ->where('done', false)
                                           ->where('sent_time', '>', time()-$max_sent_period)
                                           ->firstOrFail();
        } catch(ModelNotFoundException $e){
            unset($verification);
        }
        $result = ['result' => 'bad', 'req_sms' => $sms_code];
        if(isset($verification)){
            $code = $verification->sms_code;
            $result['bd_sms'] = $code;
            if($sms_code == $code){
                $user->sms_verified = true;
                $user->save();
                $verification->done = true;
                $verification->done_time = time();
                $verification->save();
                $result['result']  = 'done';
            }
        }

        return json_encode($result);
    }
}

?>