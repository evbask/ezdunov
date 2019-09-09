<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Carbon;

use App\LogsSession;
use App\LogsBalance;
use App\LogsRating;
use App\LogsUser;
use App\UserCard;

use App\Components\Toolkit;
use App\Components\RfiBank;

use App\Jobs\SendPush;

//use Illuminate\Support\Facades\Session;
use Laravel\Passport\HasApiTokens;

use App\Traites\UserOperations;
use App\Traites\ApiArrays;

use Exception;
use ErrorException;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use Notifiable;
    use UserOperations;
    use HasApiTokens;
    use ApiArrays;

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'name',
        'login',
        'email',
        'phone',
        'rate',
        'password',
        'settings',
        'role',
        'agree',
        'avatar',
        'promocode_id',
        'problems',
        'blocked',
        'banned',
        'deleted'
    ];

    /** массив свойств которые мы запрещаем менять из клиенсткого кода */
    private $protectedProperty = ['balance', 'bonus'];

    
    /**
     * переопределяем сетер, запрещаем менять атрибуты модели из клиентского кода
     */
    public function __set($name, $value)
	{
        if (in_array($name, $this->protectedProperty)) {
            throw new Exception("property {$name} access is denied");
        }
		parent::__set($name, $value);
	}

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token'
    ];

    protected $casts = [
        'settings' => 'array'
    ];

    protected $authRoles = [
        'admin'    => ['admin'],
        'staff'    => ['admin', 'staff'],
        'user'     => ['admin', 'staff', 'user'],
        'onlyGuest'=> ['guest']
    ];

    public static $roleNames = [
        'admin'    => 'Администратор',
        'staff'    => 'Менеджер',
        'user'     => 'Пользователь',
    ];

    /** Какие поля выводить в api json ответах для таблицы пользователей */
    protected $forSmallArray = [
        'id',
        'login',
        'name',
        'email',
        'role',
        'phone',
        'rate'
    ];

    /** Какие поля выводить в api json ответах для профиля пользователя */
    protected $forBigArray = [
        'id',
        'login',
        'name',
        'email',
        'role',
        'created_at',
        'sms_verified',
        'phone',
        'rate',
        'promocode_id',
        'register_ip',
        'avatar',
        'problems',
        'blocked',
        'banned',
        'deleted',
        'city_id'
    ];



    /** массив полей по которым будет писаться лог */
    private $arrayPropertyLogs = [
        'email',
        'phone',
        'password',
        'name',
        'role',
        'sms_verified',
        'recurrent_pay_status',
        'free_time_reservation',
        'passport_verified'
    ];

    protected static $LogClass = LogsUser::class;

    

    protected $errors;

    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Проверяем группу пользователя
     */
    public function checkGroup($group) 
    {
        if(in_array($this->role, $this->authRoles[$group])){
            return true;
        }
        return false;
    }

    public static function isAdmin(){
        return Auth::user()->checkGroup("admin");
    }
    public function getUserApiJson()
    {
        $responce = [];
        if ($this->role != 'user') {
            $responce['role'] = $this->role;
            $responce['data'] = [
                'id'          => $this->id,
                'displayName' => $this->name,
                'photoURL'    => 'assets/images/avatars/profile.jpg',
                'login'       => $this->login,
                'email'       => $this->email,
                'settings'    => $this->settings['settings'],
                'shortcuts'   => $this->settings['shortcuts'],
            ];
        } else {
            $responce['error'] = 'bad_role';
        }
        return json_encode($responce);
    }

    /**
     * Метод создает массив информации о пользователе, в нужном формате для приложения
     * @return array
     */
    public function getArrayInfo()
    {
        $array = [
            'id'                => $this->id,
            'displayName'       => $this->name,
            'login'             => $this->login,
            'email'             => $this->email,
            'emailVerified'     => $this->email_verified_at ? true : false,
            'phone'             => $this->phone,
            'phoneVerified'     => $this->sms_verified,
            'settings'          => $this->settings['settings'] ?? [],
            'shortcuts'         => $this->settings['shortcuts'] ?? [],
            'passportVerified'  => $this->passport->request_status ?? 0,
            'locale'            => $this->settings['locale'] ?? null,
            'balance'           => $this->balance,
            'bonus'             => $this->bonus,
            'isFullVerified'    => $this->isVerified(),
        ];
        return $array;
    }

    public function afterGetSmallArray($array) 
    {
        $array['role'] = $this->roleNames[$array['role']];
        return $array;
    }

    /**
     * максимально доступная сумма к выводу
     * @return int
     */
    public function maxSumWithdrawal()
    {
        return UserCard::sumCashout($this->id);
    }

    public function afterGetBigArray($array)
    {
        
        //$array['role'] = $this->roleNames[$array['role']];
        $array['created_at'] = Toolkit::GetNormalDate($array['created_at']->timestamp);//date("d m Y H.i.s", $array['created_at']->timestamp);
        $array['phone'] = Toolkit::formatPhone3($array['phone']);
        
        $passport = $this->passport;
        $fio = $passport->user_fio ?? $this->name;
        $rosPravUrl = "https://rospravosudie.com/act-$fio-q/section-acts/";
        $fioAr = Toolkit::getNameArray($fio);

        $fsspUrl = 'http://fssprus.ru/iss/ip?is%5Bvariant%5D=1&is%5Blast_name%5D=' . $fioAr['lastName']
            . '&is%5Bfirst_name%5D=' . $fioAr['firstName'] .
            '&is%5Bpatronymic%5D=' . $fioAr['patronymic'] .
            '&is%5Bdate%5D='.date("d.m.Y",$passport->date_of_birth ?? 0).'&is%5Bregion_id%5D%5B0%5D=-1';
            
        $array['fsspUrl'] =  $fsspUrl;
        $array['rosPravUrl'] = $rosPravUrl;
        $array['fio'] = $fio;
        //$array['created_at'] = $this->created_at['date'];
        $array['avatar'] = config('urls.avatars').$array['avatar'];
        if($passport) {
            $array['passport'] = $passport->getBigArray();
        } else {
            $array['passport'] = [
                'user_id' => $this->id,
                'date_of_birth' => 0,
                'passport_number' => '',
                'user_fio' => '',
                'comment_to_user' => '',
                'comment_to_manager' => '',
                'request_status' => -1,
                'photos' => []
            ];
        }
        $array['rents_num'] = $this->rent->count();
        if($this->last_activity_site){
            $array['last_visited'] = Toolkit::GetNormalDate($this->last_activity_site);
        }
        return $array;

    }

    /**
     * возращает локаль пользователя
     * @return string
     */
    public function getLocale()
    {
        return $this->settings['locale'] ?? config('app.locale');
    }

    /**
     * Устанавливает локаль пользователю
     * @param string $locale локаль
     * @return void
     * @todo скорее всего нужен конфиг доступных языков
     */
    public function setLocale(string $locale)
    {
        $this->settings['locale'] = $locale;
        $this->save();
    }

    /**
     * все ли этапы верификации прошел пользователь
     * @return boolean 
     */
    public function isVerified()
    {
        if ($this->sms_verified &&
            $this->recurrent_pay_status &&
            $this->passport_verified) {
                return true;
        } else {
            return false;
        }
    }

    /**
     * рекурентное списание
     * @todo 2019/01/20 JIorD требуется переделка
     * @param int $amount суммая списания
     * @return bool вернет результат выполнения
     */
    public function recurrentPayment(int $amount, $card = null)
    {
        if ($amount <= 0) {
            throw new Exception('Сумма должна быть положительным числом');
        }

        $bank = new RfiBank();
        try {
            $result = $bank->recurrentPayment($this, $amount, $card);
            if ($result['status'] == 'success') {
                return true;
            } else {
                $this->errors[] = $result['msg'];
                return false;
            }
        } catch (Exception $e) {
            $this->errors[] = 'Произошла непредвиденная ошибка!';
            return false;
        }
    }

    public static function newPromocode()
    {
        $promocode = new Promocode();
        $promocode->promo_code = Promocode::generatePromocode();
        $promocode->type = Promocode::T_PRIVATE;
        $promocode->discount = 10;
        $promocode->start_time = Carbon::now();
        $promocode->save();
        return $promocode->id;
    }

    /**
     * добавить задачу на отправку пуша пользователю
     * @param array $send массив с нужными параметрами
     */
    public function sendPush(array $send)
    {
        $push = LogsPush::add($this, LogsPush::T_PRIVATE, $send);
        /** ставим отправку смс в очередь */
        $job = (new SendPush($push))->
            onConnection('database')->
            onQueue('sendPush');
        dispatch($job);
    }


    /**
     * =========          мутаторы           =========
     */

    /**
     * удаляем лишние символы из имени
     * @param string $value
     * @return void
     */
    public function setName(string $value)
    {
        $this->attributes['name'] = Toolkit::clearString($value);
    }

    /**
     * стандартизируем email
     * @param string $value
     * @return void
     */


    public function setEmailAttribute(string $value)
    {
        $this->attributes['email'] = Toolkit::sanitizeEmail($value);
    }

    /**
     * стандартизируем phone
     * @param string $value
     * @return void
     */
    public function setPhoneAttribute(string $value)
    {
        $this->attributes['phone'] = Toolkit::sanitizePhone($value);
    }

    /**
     * =========          релейшены          =========
     */
    /**
     * получаем информацию о последнем девайсе пользователя
     */
    public function device()
    {
        return $this->hasOne('App\UserDevice', 'user_id', 'id');
    }

    /**
     * получаем информацию о промокоде юзера
     */
    public function promocode()
    {
        return $this->hasOne('App\Promocode', 'id','promocode_id');
    }

    /**
     * аренды пользователя
     */
    public function rent()
    {
        return $this->hasMany('App\Rent');
    }

    /**
     * получить карты с включенными рекурентыми платежами
     */
    public function payCards()
    {
        return $this->hasMany('App\UserCard', 'user_id', 'id')->
                    whereNotNull('recurrent_order_id')->
                    where('disabled', false);
    }

    /**
     * заявка на верификацию пользователя
     */
    public function passport()
    {
        return $this->hasOne('App\PasportVerifyRequest', 'user_id', 'id');
    }

    /**
     * аренды пользователя
     */
    public function rentRequests()
    {
        return $this->hasMany('App\RentRequest');
    }
}