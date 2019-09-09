<?php
namespace App;

use App\User;
use App\Components\Toolkit;

use Illuminate\Database\Eloquent\Model;

/**
 * Логи изменения бонусов
 * 
 * @property integer $id
 * @property integer $user_id
 * @property integer $type
 * @property integer $order_id
 * @property integer $before
 * @property integer $after
 * @property integer $amount
 * @property integer $date
 * 
 * @todo 2019/01/21 JIorD переделать типы
 */
class LogsBonus extends Model 
{
    const T_WRITE_ON           = 1; /** Пополнение балланса */
    const T_WRITE_OFF          = 2; /** Рекурентный платёж */

    const ARRAY_TYPES = [
        self::T_WRITE_ON     => ['text' => 'Зачисление',  'color' => ''],
        self::T_WRITE_OFF    => ['text' => 'Списание',    'color' => ''],
    ];

    protected $table = 'logs_bonus';

    protected $fillable = [
        'user_id',
        'type',
        'rent_id',
        'before',
        'after',
        'amount'
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

    public function getRentId()
    {
        return $this->rent_id ?? null;
    }

    public function getAfter()
    {
        return Toolkit::numFormatLogs($this->after);
    }

    public function getBefore()
    {
        return Toolkit::numFormatLogs($this->before);
    }

    public static function historyForApp(User $user)
    {
        $logs = self::where('user_id', $user->id)->
            orderBy('id', 'desc')->
            get();
        $logsArray = [];
        foreach ($logs as $log) {
            $logsArray[] = [
                'rent_id'   =>  $log->getRentId(),
                'before'    =>  $log->getBefore(),
                'after'     =>  $log->getAfter(),
                'amount'    =>  $log->getAmount(),
                'date'      =>  $log->date,
            ];
        }
        return $logsArray;
    }
}