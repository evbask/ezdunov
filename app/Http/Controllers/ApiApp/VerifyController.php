<?php

namespace App\Http\Controllers\ApiApp;

use Auth;
use Validator;
use Exception;

use App\User;
use App\SmsVerification;
use App\LogsSms;
use App\Components\VerifyPassport;
use App\Components\VerifyEmail;
use App\Components\Toolkit;
use App\Components\Sms;
use App\Traites\Validators;
use App\Http\Controllers\ApiAppController;
use App\Jobs\SendSms;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

/**
 * верификация пользователей
 */
class VerifyController extends ApiAppController
{
    use Validators;
    /**
     * запрос смс на верификацию телефона
     */
    public function phoneToVerify()
    {
        $user = Auth::user();
        if ($user->sms_verified) {
            return $this->sendAnswerError(['message' => 'Вы уже прошли смс проверку', 'code' => 0]);
        }

        if (SmsVerification::where('user_id', $user->id)
            ->where('done', false)
            ->where('sent_time', '>', time() - config('sms_verify.max_sent_period'))
            ->count() != 0) {
                return $this->sendAnswerError(['message' => 'Смс уже была отправлена, попробуйте позже', 'code' => 0]);
        }

        $code = Toolkit::createCode(4);

        SmsVerification::create([
            'user_id'   => $user->id,
            'phone'     => $user->phone,
            'sms_code'  => $code,
        ]);

        $LogsSms = LogsSms::create([
            'sms_target'    =>  $user->phone,
            'sms_text'      =>  $code,
            'sms_sender'    =>  'dispetcher',
            'sms_status'    =>  LogsSms::S_WAITING,
        ]);

        /** ставим отправку смс в очередь */
        $job = (new SendSms($LogsSms))->
            onConnection('database')->
            onQueue('sendSms');

        dispatch($job);

        return response()->json($this->answer);
    }

    /**
     * проверка кода смс
     */
    public function phoneVerifyCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'smsCode' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendAnswerError(['message' => 'Проверьте заполненость полей', 'code' => 0]);
        }

        $smsCode = request()->input('smsCode');
        $user = Auth::user();

        $sms = SmsVerification::where('user_id', $user->id)
            ->where('sms_code', $smsCode)
            ->where('done', false)
            ->first();
        if (empty($sms)) {
            return $this->sendAnswerError(['message' => 'Произошла ошибка, запросите верификацию телефона еще раз', 'code' => 0]);
        }

        $sms->done = true;
        $user->sms_verified = true;

        try {
            DB::beginTransaction();
            $user->save();
            $sms->save();
            DB::commit();
        } catch (Exception $e){
            DB::rollback();
            return $this->sendAnswerError(['message' => 'Что-то пошло не так =(', 'code' => 0]);
        }

        return response()->json($this->answer);
    }

    /**
     * ввод пользовательских паспортных данных
     */
    public function passportVerify(Request $request)
    {
        $validator = $this->validatePassport($request);
        if ($validator->fails()) {
            return $this->sendValidateError($validator);
        }

        $verify = new VerifyPassport($request);
        if ($verify->add()) {
            return response()->json($this->answer);
        } else {
            return $this->sendAnswerError(['message' => $verify->getErrors()[0], 'code' => 0]);
        }
    }

    /**
     * ввод почтового адреса
     */
    public function emailVerify(Request $request)
    {
        /** @todo булщит, подумать... */
        $email = Toolkit::sanitizeEmail($request->input('email'));
        $validator = Validator::make(['email' => $email], [
            'email' => ['required', 'email', 'unique:users'],
        ]);
        if ($validator->fails()) {
            return $this->sendValidateError($validator);
        }

        $user = Auth::user();
        if ($user->email_verified_at) {
            return $this->sendAnswerError(['message' => 'Ваш email подтвержден', 'code' => 0]);
        }
        
        $user->email = $email;
        
        try {
            Mail::to($user)->queue(new VerifyEmail($user));
            $user->save();
        } catch (Exception $e) {
            return $this->sendAnswerError(['message' => 'Что то пошло не так', 'code' => 0]);
        }
        return response()->json($this->answer);
    }
}
