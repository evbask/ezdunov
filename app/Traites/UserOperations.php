<?php
/**
 * вынесено в трейт в надежде разделить когда нибудь типы пользователей кур, зак, сотрудники
 * @todo возможно нужно сделать полноценный класс для операций, методы идентичны. Убрать методы обертки которые находятся в конце трейта
 * @author JIorD 2018/10/10
 */
namespace App\Traites;

use App\LogsSession;
use App\LogsBalance;
use App\LogsBonus;
use App\LogsRating;

trait UserOperations
{
    /**
     * получает баланс пользователя
     * @return integer возвращает баланс
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * уменьшить баланс на сумму
     * @param integer $sum сумма, которую нужно отнять (положительное число)
     * @param integer $typeReason ид типа операции модели Logs_freeze
     * @param integer|bool $rentId ид заказа (необязательный параметр)
     * @return void
     */
    public function balanceDecrease($sum, $typeReason, $rentId = null)
    {
        $sum = $sum < 0 ? +$sum : $sum * -1;
        $this->changeBalance($sum, $typeReason, $rentId);
    }

    /**
     * увеличить баланс на сумму
     * @param integer $sum сумма, которую нужно добавить
     * @param integer $typeReason ид типа операции модели Logs_freeze
     * @param integer|bool $rentId ид заказа (необязательный параметр)
     * @return void
     */
    public function balanceIncrease($sum, $typeReason, $rentId = null)
    {
        $sum = $sum > 0 ? +$sum : $sum * -1;
        $this->changeBalance($sum, $typeReason, $rentId);
    }

    /**
     * изменить баланс пользователя на сумму
     * @param integer $sum сумма, которую нужно добавить
     * @param integer $typeReason ид типа операции модели Logs_freeze
     * @param integer|bool $rentId ид заказа (необязательный параметр)
     * @return void
     * @throws Exception если не удалось сохранить баланс
     */
    protected function changeBalance($sum, $typeReason, $rentId = null)
    {
        $oldBalance = $this->balance;
        parent::__set('balance', $oldBalance + $sum);
        if ($this->save()) {
            $log_data = [
                'user_id' => $this->id,
                'type'    => $typeReason,
                'before'  => $oldBalance,
                'after'   => $this->balance,
                'amount'  => $sum,
                'rent_id' => $rentId
            ];
            $balance_log = LogsBalance::create($log_data);
        } else {
            $log = LogsSession::create([
                'session_id' => session()->getId(),
                'log_data' => ['userId'=> $this->id, 'ballance' => $this->balance],
                'log_message' => "userId {$this->id} текущий баланс: {$this->balance}, changeBalance({$sum}, {$typeReason}, {$rentId})"
            ]);
            $log->save();

            throw new Exception('changeBalance: Операция изменения баланса не удалась');
        }
    }

    /**
     * получает баланс пользователя
     * @return integer возвращает баланс
     */
    public function getBonus()
    {
        return $this->bonus;
    }

    /**
     * уменьшить баланс на сумму
     * @param integer $sum сумма, которую нужно отнять (положительное число)
     * @param integer $typeReason ид типа операции модели Logs_freeze
     * @param integer|bool $rentId ид заказа (необязательный параметр)
     * @return void
     */
    public function bonusDecrease($sum, $typeReason, $rentId = null)
    {
        $sum = $sum < 0 ? +$sum : $sum * -1;
        $this->changeBonus($sum, $typeReason, $rentId);
    }

    /**
     * увеличить баланс на сумму
     * @param integer $sum сумма, которую нужно добавить
     * @param integer $typeReason ид типа операции модели Logs_freeze
     * @param integer|bool $rentId ид заказа (необязательный параметр)
     * @return void
     */
    public function bonusIncrease($sum, $typeReason, $rentId = null)
    {
        $sum = $sum > 0 ? +$sum : $sum * -1;
        $this->changeBonus($sum, $typeReason, $rentId);
    }

    /**
     * изменить баланс пользователя на сумму
     * @param integer $sum сумма, которую нужно добавить
     * @param integer $typeReason ид типа операции модели Logs_freeze
     * @param integer|bool $rentId ид заказа (необязательный параметр)
     * @return void
     * @throws Exception если не удалось сохранить баланс
     */
    protected function changeBonus($sum, $typeReason, $rentId = null)
    {
        $oldBonus = $this->bonus;
        parent::__set('bonus', $oldBonus + $sum);
        if ($this->save()) {
            $log_data = [
                'user_id' => $this->id,
                'type'    => $typeReason,
                'before'  => $oldBonus,
                'after'   => $this->bonus,
                'amount'  => $sum,
                'rent_id' => $rentId
            ];
            $bonus_log = LogsBonus::create($log_data);
        } else {
            $log = LogsSession::create([
                'session_id' => session()->getId(),
                'log_data' => ['userId'=> $this->id, 'bonus' => $this->bonus],
                'log_message' => "userId {$this->id} текущие бонусы: {$this->bonus}, changebonus({$sum}, {$typeReason}, {$rentId})"
            ]);
            $log->save();

            throw new Exception('changebonus: Операция изменения бонусов не удалась');
        }
    }

    public function lowerRating($value, $reason = "-")
    {
        $ratingFrom = $this->rating;
        $this->rate -= $value;
        if ($this->save()) {
            $log_data = [
                'user_id' => $this->id,
                'before'  => $ratingFrom,
                'aftre'   => $this->rate,
                'amount'  => $value,
                'reason'  => $reason
            ];
            $log = Logs_rating::create($log_data);
            $log->save();
        }
    }
}
