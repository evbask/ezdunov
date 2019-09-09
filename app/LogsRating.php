<?php

/**
 * This is the model class for table "{{logs_rating}}".
 *
 * The followings are the available columns in table '{{logs_rating}}':
 * @property int $id
 * @property integer $user_id
 * @property string $reason
 * @property integer $before
 * @property integer $after
 * @property integer $amount
 * @property int $date
 */
namespace App;
use Illuminate\Database\Eloquent\Model;
use App\Components\Toolkit;

class LogsRating extends Model {
    
    const T_BALANCEPAYMENT     = 1; /** Пополнение балланса */
    const T_BALANCEPAYMENT_REC = 2; /** Рекурентный платёж */
    const T_RENTPAYMENT        = 3; /** Оплата за аренду */
    const T_PRERENTPAYMENT     = 4; /** Оплата за бронирование */
    const T_ADMINCORRECTION    = 5; /** Корректировка через админку */
    
    const ARRAY_TYPES     =[
        self::T_BALANCEPAYMENT     => ['text' => 'Пополнение балланса',         'color' => ''],
        self::T_BALANCEPAYMENT_REC => ['text' => 'Рекурентный платёж',          'color' => ''],
        self::T_RENTPAYMENT        => ['text' => 'Оплата за аренду',            'color' => ''],
        self::T_PRERENTPAYMENT     => ['text' => 'Оплата за бронирование',      'color' => ''],
        self::T_ADMINCORRECTION    => ['text' => 'Корректировка через админку', 'color' => ''],
    ];

    protected $table = 'logs_rating';
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


    

    

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ид',
            'user_id' => 'ид пользователя',
            'type' => 'тип операции',
            'order_id' => 'ид заказа',
            'before' => 'баланс до',
            'after' => 'баланс после',
            'amount' => 'сумма операции',
            'date' => 'дата операции',
        );
    }

    
    
    /*public static function build($arr)
    {
        $object = new self;
        $object->attributes = $arr;
        $object->id = $arr['id'];
        $object->date = $arr['date'];
        return $object;
    }*/

        
    
}