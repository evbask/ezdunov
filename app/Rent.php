<?php

namespace App;

use Auth;
use DateTime;
use Request;
use Validator;
use Exception;

use App\LogsRentStatus;
use App\Promocode;
use App\PromocodesLog;
use App\SrvVehicle;
use App\LogsBalance;
use App\Components\PreRentProblem;
use App\Components\PhotosCompletedRent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Модель аренд
 * 
 * @property int $id
 * @property int $user_id
 * @property int $promocode_id
 * @property int $tariff_id
 * @property int $vehicle_id
 * @property int $type тип аренды
 *               1 => аренда
 *               2 => бронирование
 * @property int $status
 * @property int $price
 * @property int $parent_id ид родительской аренды (при переходе с бронирования, на аренду)
 * @property string $start_time
 * @property string $end_time
 * @property string $created_at
 * @property string $updated_at
 * @property string $payment json содержащий в себе результаты расчета стоимости аренды
 *                  - paymentAmount         => цена аренды без учета скидок
 *                  - paymentActions        => сумма скидки по акциям
 *                  - paymentPromocode      => сумма скидки по промокодам
 *                  - resultPaymentAmount   => итого: цена аренды, c учетом всех акций и скидок
 *                  - bonusPayment          => итого: к оплате бонусами
 *                  - balancePayment        => итого: к оплате с баланса
 */
class Rent extends Model
{
    /**
     * @todo JIorD 2019/04/01 много лишнего, почистить проверить...
     * 
     * @var array
     */
    protected $fillable = [
        'user_id', 
        'promocode_id', 
        'tariff_id', 
        'vehicle_id', 
        'type', 
        'status', 
        'price', 
        'parent_id', 
        'start_time', 
        'end_time',
    ];

    protected $casts = [
        'payment' => 'array'
    ];

    /**
     * тип аренды
     */
    const T_RENT = 1;           /** аренда */
    const T_RESERVATION = 2;    /** бронирование */
    
    /**
     * статусы
     */
    /** начало */
    const S_BEGIN   = 1;
    /** конец */  
    const S_END     = 2;
    /** проблемы */       
    const S_PROBLEM = 3;

    public function getDateFormat()
    {
        return 'Y-m-d H:i:s';
    }

    // public function getDates()
    // {
    //     return [];
    // }

    /**
     * @var array содержит ошибки, которые 
     * произошли в ходе работы класса
     */
    protected $errors = [];

    /**
     * цена аренды без учета скидок
     * @var integer 
     */
    protected $paymentAmount = 0;

    /**
     * сумма скидки по акциям
     * @var integer 
     */
    protected $paymentActions = 0;

    /**
     * сумма скидки по промокодам
     * @var integer 
     */
    protected $paymentPromocode = 0;
    
    /**
     * итоговая цена аренды, c учетом всех акций и скидок
     * @var integer 
     */
    protected $resultPaymentAmount = 0;

    /**
     * Итого: к оплате с баланса
     * @var integer
     */
    protected $balancePayment;

    /**
     * Итого: к оплате бонусами
     * @var integer
     */
    protected $bonusPayment;

    /**
     * Фото найденных проблем, пользоавтелем, в устройстве 
     * @var PreRentProblem
     */
    protected $PreRentPhoto;

    /**
     * Фото для завершения аренды
     * @var PhotosCompletedRent
     */
    protected $PostRentPhoto;

    /**
     * Введенный промокод
     * @var PromocodesLog
     */
    protected $InputPromo;

    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * заполнение необходимых атрибутов пришедших из запроса
     * 
     * @todo JIorD 2019/04/01 подумать... по правильному, скорее всего, нужно отказаться 
     *       от использования Request внутри класса
     */
    public function build()
    {
        $this->user_id = Request::user()->id;

        $this->InputPromo = PromocodesLog::whereNull('rent_id')->
            where('user_id', $this->user_id)->
            orderBy('id', 'asc')->
            first();

        $this->promocode_id = $this->InputPromo->promocode_id ?? null;
        
        $this->vehicle_id = Request::input('vehicleId');

        /**
         * @todo добавить актуальные проверки!!!!
         * сделано для наглядности, как должно быть
         */
        if ($this->vehicle->id ?? false) {
            if ($this->vehicle->status != SrvVehicle::S_FREE) {
                $this->errors[] = 'Это устройство не может быть арендовано';
            }
        } else {
            $this->errors[] = 'Устройство не найдено.';
            return false;
        }

        $this->tariff_id = $this->vehicle->tariff->
                            where('type_rent', Request::input('rentType'))->
                            where('type_payment', Tariff::T_POST)-> /** задано в ручную так как отказались от предоплаты */
                            first()->id ?? null;

        /**
         * это проверки логики системы
         * в идеале сделать пуши или смс если попали хоть в одно из условий
         * т.к что то работает не так
         */
        if ($this->tariff_id ?? false) {
            if (!in_array($this->tariff->type_payment, [Tariff::T_PRE, Tariff::T_POST])) {
                $this->errors[] = 'Не существующий тип оплаты.';
            }
            if (!$this->tariff->enable) {
                $this->errors[] = 'Данный тариф не актуальный';
            }
            if ($this->vehicle->type != $this->tariff->type_vehicle) {
                $this->errors[] = 'Тариф не соответствует устройству';
            }
        } else {
            $this->errors[] = 'Попробуйте позже, либо обратитесь в службу поддержки.';
            return false;
        }
        
        $this->type = $this->tariff->type_rent;
        $this->price = $this->tariff->price;

        $this->parent_id = null;

        $this->addProblem();

        /**
         * на данный момент не используется
         */
        if ($this->tariff->type_payment == Tariff::T_PRE) {
            $this->setPaymentVar();
        }
    }

    /**
     * условия для создания аренды
     */
    public function conditionsAdd()
    {
        if ($this->isErrors()) {
            return false;
        }

        /** eсли аренда после бронирования, второй раз проверять условия не нужно */
        if (!$this->parent_id) {
            /** нет ли взятых аренд */
            $checkRents = $this->user->rent->where('status', '!=', self::S_END)->first();
            if ($checkRents) {
                $this->errors[] = 'У вас уже есть аренда';
            }

            /** условия по балансу */
            if ($this->user->balance >= 0) {
                if ($this->tariff->type_payment == Tariff::T_PRE && $this->user->balance < $this->resultPaymentAmount) {
                    $this->errors[] = 'На балансе у вас должно быть не менее ' . abs($this->resultPaymentAmount) . ' р.';
                }
            } else {
                /** не делаем рекурент, если были ошибки */
                if (empty($this->errors) && $this->user->recurrentPayment(abs($this->user->balance))) {
                    // делаем ничего
                } else {
                    $this->errors[] = 'Вам необходимо пополнить баланс на ' . abs($this->user->balance) . ' р.';
                }
            }
        }
    }

    /**
     * создание аренды
     * @return void
     */
    public function add()
    {
        $this->conditionsAdd();

        if ($this->isErrors()) {
            return false;
        }
        
        $this->vehicle->status = SrvVehicle::S_RENT;

        $this->changeStatus(self::S_BEGIN);

        $this->start_time = (new DateTime())->format('Y-m-d H:i:s');
        $this->end_time = $this->tariff->type_payment == Tariff::T_PRE ? 
            (new DateTime('+' . $this->tariff->quantity . ' min'))->format('Y-m-d H:i:s') : 
            null;

        try {
            DB::beginTransaction();

            $this->save();
            $this->vehicle->save();

            if ($this->PreRentPhoto ?? false) {
                $this->PreRentPhoto->add();
            }

            /** "используем" промокод, если он был найден */
            if ($this->promocode_id ?? false) {
                $this->InputPromo->rent_id = $this->id;
                $this->InputPromo->save();
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $this->errors[] = 'Ошибка операции, обратитесь в службу поддержки.';
            return false;
        }
        
        return true;
    }

    public function conditionsStart()
    {
        if ($this->type != self::T_RESERVATION) {
            $this->errors[] = 'Текущая тип аренды должен быть бронирование.';
        }
        if ($this->status == self::S_END) {
            $this->errors[] = 'Аренда уже была завершена.';
        }
        if ($this->user_id != Auth::user()->id) {
            $this->errors[] = 'Доступ запрещен';
        }
    }

    /**
     * старт аренды, в том случае если была бронь
     */
    public function start()
    {
        $this->conditionsStart();
        
        if ($this->isErrors()) {
            return false;
        }
        
        $rent = new self();

        $rent->user_id = $this->user_id;
        $rent->promocode_id = $this->promocode_id;
        $rent->vehicle_id = $this->vehicle_id;

        $rent->addProblem();

        $rent->tariff_id = Tariff::where('type_rent', self::T_RENT)->
                            where('enable', true)->
                            where('type_vehicle', $this->vehicle->type)->
                            first()->id ?? null;

        if (!$rent->tariff_id) {
            $this->error[] = 'Несуществующий тариф';
            return false;
        }

        $rent->type = self::T_RENT;
        $rent->price = $rent->tariff->price;
        $rent->parent_id = $this->id;
        
        /**
         * @todo переделать
         */
        if (!$this->close(false)) {
            return false;
        }
        if (!$rent->add()) {
            $this->errors += $rent->getErrors();
            return false;
        }

        return true;
    }

    /**
     * проверки завершения аренды
     */
    public function conditionsClose()
    {
        if ($this->user_id != Auth::id()) {
            $this->errors[] = 'Вы не можете завершить чужую аренду';
        }
        if ($this->status == self::S_END) {
            $this->errors[] = 'Вы не можете завершить аренду дважды';
        }
        if ($this->type == self::T_RENT) {
            $validator = Validator::make(Request::all(), [
                'photo'    => ['required', 'array', 'min:2', 'max:2'],
                'photo.*'  => ['image', 'mimes:jpg,jpeg,png'],
            ]);
            if ($validator->fails()) {
                $this->errors[] = $validator->errors()->all()[0];
            }
        }
    }

    /**
     * завершение аренды
     * @param bool $recPay нужно ли списывать деньги с карты
     */
    public function close($recPay = true)
    {
        $this->conditionsClose();

        if ($this->isErrors()) {
            return false;
        }

        $this->changeStatus(self::S_END);

        if ($this->type == self::T_RENT) {
            $this->user->free_time_reservation = true;

            /** сохраняем фото на диск */
            $photos = new PhotosCompletedRent();
            if (!$photos->build($this)) {
                $this->errors += $photos->getErrors();
                return false;
            }

        } else {
            $this->user->free_time_reservation = false;
        }

        $this->vehicle->status = SrvVehicle::S_FREE;

        try {
            DB::beginTransaction();
            
            $this->payment($recPay);

            $this->save();
            $this->user->save();
            $this->vehicle->save();

            if ($this->type == self::T_RENT) {
                $photos->add();
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $this->errors[] = 'Произошла ошибка во время завершения аренды.' . $e->getMessage();
            return false;
        }

        return true;
    }

    /**
     * производит оплату по аренде
     * списывает с баланс, бонусы
     * создается рекурентый платеж
     * @return void
     */
    public function payment($recPay = false)
    {
        if ($this->tariff->type_payment == Tariff::T_POST) {
            $this->end_time = (new DateTime())->format('Y-m-d H:i:s');
            $this->setPaymentVar();

            $parentBalancePayment = 0;
            if ($this->parent_id) {
                $parentBalancePayment = $this->parentRent->payment['balancePayment'] ?? 0;
            }

            /**
             * @todo вынести в крон или планировщик заданий
             */
            // if ($recPay && ($parentBalancePayment + $this->balancePayment) > 0) {
            //     $this->user->recurrentPayment($parentBalancePayment + $this->balancePayment);
            // }

            if ($this->balancePayment != 0) {
                $this->user->balanceDecrease($this->balancePayment, LogsBalance::T_RENTPAYMENT, $this->id);
            }
            if ($this->bonusPayment != 0) {
                $this->user->bonusDecrease($this->bonusPayment, LogsBonus::T_WRITE_OFF, $this->id);
            }
        }
    }

    /**
     * расчет итоговой стоимости и необходимых значений
     * из скидок по акциями и кодам берем максильную
     * если несколько акций берем максильную и сравниваем с промокодом
     * @throws Exception
     * @return void
     */
    public function setPaymentVar()
    {
        $this->paymentAmount = $this->price * $this->getTime();
        $this->paymentActions = 0;  // @todo добавить какой то метод
        $this->paymentPromocode = $this->promocode_id ? $this->promocode->getDiscount($this) : 0;
        $this->resultPaymentAmount = $this->paymentAmount - max($this->paymentActions , $this->paymentPromocode);
        
        $diff = $this->user->bonus - $this->resultPaymentAmount;
        if ($diff >= 0) { 
            /**
             * бонусов достаточно
             */
            $this->bonusPayment = $this->resultPaymentAmount;
            $this->balancePayment = 0;
        } else {
            /**
             * бонусов не хватает, списываем баланс
             */
            $this->bonusPayment = $this->user->bonus > 0 ? $this->user->bonus : 0;
            $this->balancePayment = $this->resultPaymentAmount - $this->bonusPayment;
        }
        if ($this->resultPaymentAmount <= 0) {
            throw new Exception('Произошла ошибка, обратитесь в службу поддержки'); /** @todo Разобраться с ошибками!! */
        }

        $this->payment = [
            'paymentAmount'         => $this->paymentAmount,
            'paymentActions'        => $this->paymentActions,
            'paymentPromocode'      => $this->paymentPromocode,
            'resultPaymentAmount'   => $this->resultPaymentAmount,
            'bonusPayment'          => $this->bonusPayment,
            'balancePayment'        => $this->balancePayment
        ];
    }

    /**
     * получить время аренды в минутах
     * @return integer
     * @throws Exception
     */
    public function getTime()
    {
        switch ($this->tariff->type_payment) {
            case Tariff::T_PRE:
                return $this->tariff->quantity;
            case Tariff::T_POST:
                $startTime = strtotime($this->start_time);
                $endTime = strtotime($this->end_time);
                return ceil(($endTime - $startTime) / 60);
            default:
                throw new Exception('Тип оплаты не существует');
        }
    }

    public function isErrors()
    {
        if (!empty($this->errors)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * изменение статуса аренды
     * @param integer $status
     * @return void
     * 
     * @todo подумать как писать лог
     */
    public function changeStatus(int $status)
    {
        // LogsRentStatus::create([
        //     'rent_id'   =>  $this->id,
        //     'user_id'   =>  Auth::user()->id,
        //     'status'    =>  $status,
        //     'date'      =>  (new DateTime())->format('Y-m-d H:i:s'),
        // ]);
        $this->status = $status;
    }

    /**
     * добавить фото с проблемами девайса
     */
    public function addProblem()
    {
        if ($this->type == self::T_RENT) {
            $this->PreRentPhoto = new PreRentProblem();
            $this->PreRentPhoto->build($this);
        }
    }

    /**
     * вернет массив параметров для приложения
     * @return array
     */
    public function getArrayInfoApp()
    {
        return [
            'id'                => $this->id,
            'status'            => $this->status,
            'price'             => $this->price,
            'startTime'         => $this->start_time,
            'end_time'          => $this->end_time,
            'bonusPayment'      => $this->payment['bonusPayment'],
            'balancePayment'    => $this->payment['balancePayment'],
        ];
    }




    /**
     * =========          релейшены          =========
     */
    public function promocode()
    {
        return $this->hasOne('App\Promocode', 'id', 'promocode_id');
    }

    public function tariff()
    {
        return $this->belongsTo('App\Tariff');
    }

    public function promocodesLogs()
    {
        return $this->hasMany('App\PromocodesLog');
    }

    public function preRentProblems()
    {
        return $this->hasMany('App\PreRentProblem');
    }

    public function vehicle()
    {
        return $this->hasOne('App\SrvVehicle', 'id', 'vehicle_id');
    }

    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    public function parentRent()
    {
        return $this->hasOne('App\Rent', 'id', 'parent_id');
    }
}
