<?php
namespace App;

use App\Components\Toolkit;

use Illuminate\Database\Eloquent\Model;

/**
 * Логи изменения баланса
 * 
 * @property integer $id
 * @property integer $user_id
 * @property integer $type
 * @property integer $order_id
 * @property integer $before
 * @property integer $after
 * @property integer $amount
 * @property integer $date
 */
class LogsBalance extends Model 
{
    const T_BALANCEPAYMENT     = 1; /** Пополнение балланса */
    const T_BALANCEPAYMENT_REC = 2; /** Рекурентный платёж */
    const T_RENTPAYMENT        = 3; /** Оплата за аренду */
    const T_PRERENTPAYMENT     = 4; /** Оплата за бронирование */
    const T_ADMINCORRECTION    = 5; /** Корректировка через админку */
    const T_CASHOUT_REQUEST    = 6; /** Вывод средств */

    const ARRAY_TYPES = [
        self::T_BALANCEPAYMENT     => ['text' => 'Пополнение балланса',         'color' => ''],
        self::T_BALANCEPAYMENT_REC => ['text' => 'Рекурентный платёж',          'color' => ''],
        self::T_RENTPAYMENT        => ['text' => 'Оплата за аренду',            'color' => ''],
        self::T_PRERENTPAYMENT     => ['text' => 'Оплата за бронирование',      'color' => ''],
        self::T_ADMINCORRECTION    => ['text' => 'Корректировка через админку', 'color' => ''],
        self::T_CASHOUT_REQUEST    => ['text' => 'Вывод средств',               'color' => ''],
    ];

    protected $table = 'logs_balance';

    protected $fillable = [
        'user_id',
        'type',
        'before',
        'after',
        'amount',
        'rent_id'
    ];
    public $timestamps = false;

    /**
     * получает текстовое представление
     */
    public function getText()
    {
        $arr = self::ARRAY_TYPES;
        if (array_key_exists($this->type, $arr)) {
            return $arr[$this->type]['text'];
        } else {
            return "Не определенно.";
        }
    }

    /**
     * получает цветовое представление
     */
    public function getColor()
    {
        $arr = self::ARRAY_TYPES;
        if (array_key_exists($this->type, $arr)) {
            return $arr[$this->type]['color'];
        } else {
            return "585858";
        }
    }

    /**
     * возвращает текстовое представление типа операции
     * оформеленное с помощью html цветовой индикацией
     */
    public function getTextHtml()
    {
        return '<font style="color:#' . $this->getColor() . ';">' . $this->getText() . '</font>';
    }

    public function getAmount()
    {
        return $this->amount > 0 ? '+' . Toolkit::numFormatLogs($this->amount) : Toolkit::numFormatLogs($this->amount);
    }
}