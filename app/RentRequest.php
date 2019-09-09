<?php

namespace App;

use Request;
use Exception;

use App\SrvVehicle;
use App\Logs_rfi_bank;
use App\RentRequestsTariffs;
use App\Components\RfiBank;
use App\Jobs\WriteOfMoney;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * аренда самоката
 * @param string $address_to
 * @param string $address_from
 * @param string $time_to
 * @param string $time_from
 * @param integer $status
 * @param string $created_at
 * @param string $updated_at
 * @param integer $tariff_id ид тарифа
 * @param integer $paymen итого к оплате
 */
class RentRequest extends Model
{
    protected $table = 'rent_requests';
    public $primaryKey = 'id';

    /**
     * время аренды
     * @var integer
     */
    protected $rentTime = 0;

    /**
     * @var array содержит ошибки, которые 
     * произошли в ходе работы класса
     */
    protected $errors = [];

    /**
     * заявка отправлена
     */
    const T_REQUEST_SENT            = 1;
    /**
     * заявка оплачена
     */
    const T_REQEST_PAID             = 2;
    /**
     * самокат доставлен
     */
    const T_REQUEST_DELIVERED       = 3;
    /**
     * аренда завершена
     */
    const T_REQUEST_COMPLETED       = 4;
    /**
     * заявка отменена
     */
    const T_REQUEST_CANCEL          = 5;

    const T_STRINGS = [
        self::T_REQUEST_SENT            => 'ожидает доставки',
        self::T_REQUEST_DELIVERED       => 'ожидает забора',
        self::T_REQUEST_COMPLETED       => 'завершена'
    ];

    protected $fillable = [
        'address_to',
        'address_from',
        'time_to',
        'time_from',
        'status',
        'comment'
    ];

    /**
     * билд объекта
     * @return bool
     */
    public function build()
    {
        $this->user_id = Request::user()->id;
        $this->status = self::T_REQUEST_SENT;
        $this->address_to = $this->getAddressToRequest();
        $this->address_from = $this->getAddressFromRequest();
        $this->time_to = Request::input('time_to');
        $this->time_from = Request::input('time_from');

        $this->tariff_id = RentRequestsTariffs::where('active', true)->first()->id ?? false;

        /** нет активных тарифов */
        if (!$this->tariff_id) {
            $this->errors[] = 'Произошла ошибка, попробуйте позже или обратитесь в службу поддеркжи';
            return false;
        }

        $this->rentTime = $this->getTime();
        if ($this->rentTime < 1) {
            $this->errors[] = 'Время аренды от 1 дня';
        }

        $this->setPaymentVar();

        return !$this->isErrors();
    }

    /**
     * условия для создания аренды
     * @return bool
     */
    public function conditionsAdd()
    {
        /** проверка наличия свободных самокатов */
        if (SrvVehicle::getCountFree() < 1) {
            $this->errors[] = "В данный момент слишком большое количество заявок на аренду, попробуйте позже.";
        }

        /** условия по балансу */
        if ($this->user->balance < 0) {
            $this->errors[] = 'Вам необходимо пополнить баланс на ' . abs($this->user->balance) . ' р.';
        }

        /** не более 1 активной заявки */
        $checkRents = $this->user->rentRequests->whereNotIn('status', [self::T_REQUEST_COMPLETED, self::T_REQUEST_CANCEL])->first();
        if ($checkRents) {
            $this->errors[] = 'У вас уже есть активная аренда';
        }

        return !$this->isErrors();
    }

    /**
     * создание заявки на аренду
     */
    public function add()
    {
        $this->conditionsAdd();

        if ($this->isErrors()) {
            return false;
        }
        
        try {
            DB::beginTransaction();

            $this->save();
            echo $this->id;
            $this->payment();

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $this->errors[] = 'Ошибка операции, обратитесь в службу поддержки.';
            return false;
        }
        return true;
    }

    /**
     * получить адрес из запроса
     * @return string 
     */
    protected function getAddressToRequest()
    {
        $request = [
            Request::input('to_street'),
            Request::input('to_house'),
            Request::input('to_kv'),
        ];
        return implode(", ", $request);
    }

    /**
     * получить адрес из запроса
     * @return string 
     */
    protected function getAddressFromRequest()
    {
        $request = [
            Request::input('from_street'),
            Request::input('from_house'),
            Request::input('from_kv'),
        ];
        return implode(", ", $request);
    }

    /**
     * получить время аренды в днях
     * @return integer
     */
    public function getTime()
    {
        return (int)ceil((strtotime($this->time_from) - strtotime($this->time_to)) / 60 / 60 / 24);
    }

    /**
     * проверка массива ошибок
     * @return bool
     */
    public function isErrors()
    {
        if (!empty($this->errors)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * получить массив ошибок
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * количество заявок в ожидании
     * @return integer
     */
    public static function getCountWaiting()
    {
        return self::whereIn('status', [self::T_REQUEST_SENT, self::T_REQEST_PAID])->count();
    }

    /**
     * производит оплату по аренде,
     * создается задание на проведения рекуреного платежа
     * @return void
     */
    protected function payment()
    {
        $rfi = Logs_rfi_bank::create([
            'user_id'           => $this->user_id,
            'summ'              => $this->payment,
            'status'            => Logs_rfi_bank::STATUS_WAITING,
            'service_id'        => RfiBank::SERVICE_ID,
            'phone'             => $this->user->phone,
            'type'              => RfiBank::PAYMENT_TYPE,
            'email'             => $this->user->email,
            'rent_request_id'   => $this->id
        ]);
        
        dispatch((new WriteOfMoney($rfi))->
            onConnection('database')->
            onQueue('WriteOfMoney'));
    }

    /**
     * расчет итоговой стоимости
     * @throws Exception
     * @return void
     */
    public function setPaymentVar()
    {
        $this->payment = $this->tariff->price * $this->rentTime;
        if ($this->payment <= 0) {
            throw new Exception('Произошла ошибка, обратитесь в службу поддержки');
        }
    }




    /**
     * =========          релейшены          =========
     */
    /**
     * установленный тариф
     * @return RentRequestsTariffs|null
     */
    public function tariff()
    {
        return $this->belongsTo('App\RentRequestsTariffs');
    }

    /**
     * пользователя создавший заявку
     * @return User|null
     */
    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }
}
