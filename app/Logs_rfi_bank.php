<?php

/**
 * This is the model class for table "{{logs_rfi_bank}}".
 *
 * The followings are the available columns in table '{{logs_rfi_bank}}':
 * @property integer $id
 * @property integer $user
 * @property integer $status
 * @property integer $sum
 * @property integer $sum_returned
 * @property integer $service_id
 * @property integer $transaction_id
 * @property string $type
 * @property integer $partner_id
 * @property integer $partner_income
 * @property string $phone
 * @property string $email
 * @property string $card
 * @property string $cardholder
 * @property string $result_str
 * @property string $created
 * @property string $updated
 */
namespace App;

use App\Components\RfiBank; 

use Illuminate\Database\Eloquent\Model;

class Logs_rfi_bank extends Model
{

    protected $table = 'logs_rfi_bank';
    
    const CREATED_AT = 'created';
    const UPDATED_AT = 'updated';

    const STATUS_WAITING        = -1;
    const STATUS_NOT_FINISHED   = 0;
    const STATUS_FINISHED       = 1;
    const STATUS_ERROR          = 2;
    const STATUS_PARTIALRETURN  = 3;
    const STATUS_FULLRETURN     = 4;

    /** статусы возвратов платежей */
    const S_CASHOUT_SUCCESS    = 1;
    const S_CASHOUT_ERROR      = 2;

    /**
     * @todo открыто много лишнего
     */
    protected $fillable = [
        'user_id',
        'status',
        'summ',
        'service_id',
        'phone',
        'type',
        'email',
        'rent_request_id'
    ];

    public function allTypeNames()
    {
        return [
            'Банковская карта' => ['spg', 'spg_rfi2', 'spg_rfi4', 'spg_rfi6'],
            'Карта (тест)' => ['spg_test', 'spg_test_rfi2', 'spg_test_rfi4', 'spg_test_rfi6'],
            'Яндекс-деньги' => ['ym', 'ym_rfi'],
            'Qiwi' => ['qiwi'],
            'Web-money' => ['wm'],
            'МТС' => ['mtsbank_mts_mc'],
            'Билайн' => ['beeline_rfi_mc'],
            'Мегафон' => ['megafon_rfi_round_mc', 'megafon'],
            'Теле2' => ['mm_tele2_rfi_mc', 'bc_tele2_mc']
        ];
    }

    public function typeName()
    {
        $allTypeNames = $this->allTypeNames();

        foreach ($allTypeNames as $name => $types) {
            if (in_array($this->type, $types))
                return $name;
        }

        return '';
    }

    public function getStatusName()
    {
        $statuses = [
            static::STATUS_NOT_FINISHED => 'Не завершён',
            static::STATUS_FINISHED => 'Оплачен',
            static::STATUS_ERROR => 'Ошибка',
            static::STATUS_PARTIALRETURN => 'Частичный возврат',
            static::STATUS_FULLRETURN => 'Полный возврат'
        ];

        return $statuses[$this->status];
    }

    public function getTransactionId()
    {
        return $this->transaction_id;
    }

    /**
     * получает последнюю попытку рекурентного списания
     * @param integer $userId ид пользователя
     * @return Logs_rfi_bank
     */
    public static function getLastRecurrentPayment($userId)
    {
        $log = Logs_rfi_bank::where('user_id', $userId)
                     ->where('recurrent_type', 'LIKE', '%next%')
                     ->orderBy('id', 'desc')->first();
        return $log;
    }

    /**
     * выводит текстовую информацию по платежу
     * @param object $logs объект Logs_rfi_bank
     * @return string
     */
    public function getInfoRecurrentPayment()
    {
        return "{$this->updated}, сумма: {$this->sum} р., результат операции: {$this->getStatusName()}, описание: {$this->result_str}";
    }

    /**
     * Отправка заявки на возврата
     */
    public function sendReturnPaymentRequest($sum = false)
    {
        $this->sum_returned = $this->summ;

        $params = [
            'version' => '2.0',
            'tid' => $this->transaction_id,
            'amount' => $this->summ,
            'refund_ext_id' => time(),
        ];

        $bank = new RfiBank($params);
        $result = $bank->refund();

        if ($result['status'] == 'success') {
            $this->result_cashout_str = 'Возврат успешно совершён';
            $this->status_cashout = self::S_CASHOUT_SUCCESS;
            $this->status = self::STATUS_FULLRETURN;
        } else {
            $this->result_cashout_str = $result['message'] ?? 'Неизвестная ошибка.';
            $this->status_cashout = self::S_CASHOUT_ERROR;
        }
        $this->save();
        
        if ($result['status'] == 'success') {
            return true;
        } else {
            return false;
        }

    }




    /**
     * =========          релейшены          =========
     */
    /**
     * объект пользователя
     */
    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    public function rentRequest()
    {
        return $this->hasOne('App\RentRequest', 'id', 'rent_request_id');
    }
}
