<?php

namespace App\Http\Controllers\ApiApp;

use Auth;
use Validator;

use App\User;
use App\UserDevice;
use App\SmsVerification;
use App\SmsPasswordReset;
use App\LogsSms;
use App\Components\Sms;
use App\Components\Toolkit;
use App\Components\ImageWebp;
use App\Jobs\SendSms;
use App\Traites\Validators;
use App\Http\Controllers\ApiAppController;
use App\Models\Passport\PersonalAccessClient;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

/**
 * контроллер отвечает за получении информации об устройствах
 * 
 * @see https://laravel.com/docs/5.7/passport#personal-access-tokens
 */
class AuthenticationController extends ApiAppController
{
    /** 
     * Трейт для отправки емейл на восстановление пароля
     */
    use SendsPasswordResetEmails, Validators;

    /**
     * поле для логина по умолчанию
     */
    protected $username = 'email';

    /**
     * вход
     * @param object Request
     * @todo реализовать трейт ThrottlesLogins, запретить подбор пароля
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendValidateError($validator);
        }

        $this->setUsernameResponse();

        if (Auth::attempt($request->only($this->username(), 'password'))) {
            $user = Auth::user();

            $uD = UserDevice::add($request);

            /**
             * @todo уязвиомость, длина строки
             */
            $token = $user->createToken($uD ?? 'ApiApp')->accessToken;

            $this->answer['userInfo'] = $user->getArrayInfo();
            $this->answer['token'] = $token;

            return response()->json($this->answer);
        } else {
            return $this->sendAnswerError(['message' => 'Не верный логин или пароль', 'code' => 0]);
        }
    }

    /**
     * @todo регистрация
     */
    public function registry(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone'     => ['required', 'string', 'min:5', 'max:11', 'unique:users'],
            'password'  => ['required', 'string', 'min:6'],
            'agree'     => ['required', 'boolean'],
            'avatar'    => ['image', 'mimes:jpg,jpeg,png']
        ]);
        if ($validator->fails()) {
            return $this->sendValidateError($validator);
        }

        $path = config('folders.avatars');
        $image = new ImageWebp();
        $image->build($request->avatar, $path);
        $image->convert();

        $user = User::create([
            'phone'         => $request->phone,
            'password'      => Hash::make($request->password),
            'role'          => 'user',
            'settings'      => ['settings' => [], 'shortcuts' => []],
            'agree'         => $request->agree,
            'avatar'        => $image->getNames()[0] ?? 'profile.jpg',
            'register_ip'   => Toolkit::getRealIp(),
            'promocode_id'  => User::newPromocode(),
        ]);
        
        Auth::login($user);

        $uD = UserDevice::add($request);

        $token = $user->createToken($uD ?? 'ApiApp')->accessToken;

        $this->answer['userInfo'] = $user->getArrayInfo();
        $this->answer['token'] = $token;
        return response()->json($this->answer);
    }

    /**
     * разлогинить пользователя, уничтожить токен
     * @todo возможно следует уничтожать все токены????
     * 
     * @todo нужен ли logout? сейчас он не работает
     */
    public function logout()
    {
        $user = Auth::user();
        if (Auth::check()) {
            // уничтожить все токены
            // $userTokens = $usere->tokens;
            // foreach($userTokens as $token) {
            //     $token->revoke();   
            // }
            // Auth::logout();
            $user->token()->revoke();
            return response()->json($this->answer);
        } else {
            return $this->sendAnswerError(['message' => 'Вы не авторизованы', 'code' => 0]);
        }
    }

    /**
     * сброс пароля
     * если пользователь ввел:
     * - email будет отправлено письмо с ссылкой для восстановления пароля
     * - phone будет отправлено смс с кодом для восстановления пароля
     */
    public function passwordReset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendValidateError($validator);
        }

        $this->setUsernameResponse();

        $user = User::where($this->username(), 
            request()->input($this->username()))
            ->first();

        if (!$user ?? false) {
            return $this->sendAnswerError(['message' => 'Такого логина не существует', 'code' => 0]);
        }

        if ($this->username() == 'email') {
            $this->answer['type'] = 0;

            /**
             * Отправить письмо для восстановления
             * @see https://laravel.com/docs/5.7/passwords
             * использует трейт Illuminate\Foundation\Auth\SendsPasswordResetEmails;
             */
            $this->sendResetLinkEmail($request);

        } else {
            $this->answer['type'] = 1;
            /**
             * @todo по хорошему написать единный методы для сайта и апи
             */
            $phone = request()->input('phone');
            /**
             * @todo разработать логику отправки смс
             * нельзя отправлять более N раз в сутки
             * так же запрет на кол. с одного ип
             * слишком часто отправлять смс тоже нельзя
             */
            $sec = config('sms_verify.max_sent_period');
            if (SmsPasswordReset::where('user_id', $user->id)
                // ->where('phone', $phone)
                ->where('success', false)
                ->where('created_at', '>', (new \DateTime())->modify("-{$sec} sec")->format('Y-m-d H:i:s'))
                ->count() != 0) {
                    return $this->sendAnswerError(['message' => 'Смс уже была отправлена, попробуйте позже', 'code' => 0]);
            }

            $code = Toolkit::createCode(4);
            
            SmsPasswordReset::create([
                'user_id'   => $user->id,
                'phone'     => $phone,
                'code'      => $code,
            ]);

            $LogsSms = LogsSms::create([
                'sms_target'    =>  $phone,
                'sms_text'      =>  $code,
                'sms_sender'    =>  'ezdunov',
                'sms_status'    =>  LogsSms::S_WAITING,
            ]);

            /** ставим отправку смс в очередь */
            $job = (new SendSms($LogsSms))->
                onConnection('database')->
                onQueue('sendSms');
            dispatch($job);
        }
        return response()->json($this->answer);
    }

    /**
     * сброс пароля с помощью телефона
     */
    public function passwordSmsReset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone'     =>  'required',
            'smsCode'   =>  'required',
            'password'  =>  'required'
        ]);
        if ($validator->fails()) {
            return $this->sendValidateError($validator);
        }

        $phone = request()->input('phone');
        $phone = Toolkit::sanitizePhone($phone);
        $smsCode = request()->input('smsCode');
        /**
         * @todo подумать над выборкой!
         */
        $sms = SmsPasswordReset::where('phone', $phone)
            ->where('code', $smsCode)
            ->where('success', false)
            ->first();
        if (!$sms ?? false) {
            return $this->sendAnswerError(['message' => 'Введенный код не верный', 'code' => 0]);
        }
        

        /**
         * доп проверка, что пользователь не сменил телефон
         * @todo добавить логи
         */
        $user = User::where('phone', $phone)
            ->where('id', $sms->user_id)
            ->first();

        if (!$user ?? false) {
            return $this->sendAnswerError(['message' => 'Произошла ошибка, обратитесь в службу поддержки', 'code' => 0]);
        }

        $sms->success = true;
        $user->password = Hash::make(request()->input('password'));

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
     * ======================
     * Вспомогательные методы
     * ======================
     */
    
    /**
     * определяем из запроса, что было передано email или phone
     * добавляем в запрос необходимые ключи с значениями,
     * а так же 'очищаем' введенные данные
     * @return void
     */
    public function setUsernameResponse()
    {
        $login = request()->input('username');
        $this->username = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        if ($this->username == 'email') {
            $login = Toolkit::sanitizeEmail($login);
        } else {
            $login = Toolkit::sanitizePhone($login);
        }
        request()->merge([$this->username => $login]);
    }
 
    /**
     * возвращает текущий тип 'логина'
     * @return string
     */
    public function username()
    {
        return $this->username;
    }
}
