<?php
namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Validator;

/**
 * базовый контроллер для апи приложения
 */
class ApiAppController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        /**
         * проверка верификации
         * и ее исключения
         */
        $this->middleware('isVerified')->
                except([
                    /** auth */
                    'login',
                    'registry',
                    'logout',
                    'passwordReset',
                    'passwordSmsReset',

                    /** device */
                    'getAvailableDevices',

                    /** file */
                    'getAvatar',
                    'getPassportVerifyPhoto',

                    /** payment */
                    'rfi',

                    /** support */
                    'getMessages',
                    'sendMessage',

                    /** test @todo убрать после разработки */
                    'bonus',
                    'balance',

                    /** user */
                    'getUserInfo',
                    /** @todo убрать после разработки */
                    'setAttribute',

                    /** verify */
                    'phoneToVerify',
                    'phoneVerifyCode',
                    'passportVerify',
                    'emailVerify',
                ]);
        $this->middleware('auth.app:api')->
                except([
                    /** auth */
                    'login',
                    'registry',
                    'passwordReset',
                    'passwordSmsReset',
                ]);
    }

    /**
     * ответ по умолчанию
     */
    protected $answer = [
        'success'   =>  true,
    ];

    public function sendAnswerError(Array $array, $code = 400)
    {
        $this->answer['success'] = false;
        $this->answer += $array;
        return response()->json($this->answer, $code);
    }

    /**
     * условились на первой ошибке, можно отдавать все
     */
    public function sendValidateError(Validator $validator, $code = 400) 
    {
        $this->answer['success'] = false;
        $this->answer += [
            'message' => $validator->errors()->all()[0], 
            'code' => 0
        ];
        return response()->json($this->answer, $code);
    }
}