<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * платежные карты пользователя
 * @property integer $id
 * @property integer $user_id
 * @property string $card
 * @property string $cardholder
 * @property string $sum
 * @property string $card_binding_id
 * @property integer $recurrent_order_id Если recurrent_order_id null, то считаем, что карта не подключена к рек. платежам
 * @property bool $disabled false - карта видна, true карта скрыта
 */
class UserCard extends Model 
{
    protected $table = 'user_card';

    protected $fillable = [
        'user_id',
        'card',
        'cardholder',
        'summ',
        'card_binding_id',
        'recurrent_order_id',
        'disabled',
    ];

    /**
     * отображение типа операции в виде строки для вывода
     */
    public function getTypeString()
    {
        return "РФИ банк";
    }

    /**
     * отображение типа операции
     */
    public function getType()
    {
        return Logs_cashout::PAYMENT_TYPE_RFI;
    }

    /*
    *   Создает новую запись в таблице
    */
    public function add($user_id, $card, $cardholder, $card_binding_id, $recurrent_order_id, $lastupdate, $disabled)
    {
        $userCard = new self;
        $userCard->user_id = $user_id;
        $userCard->card = $card;
        $userCard->cardholder = $cardholder;
        $userCard->card_binding_id = $card_binding_id;
        $userCard->recurrent_order_id = $recurrent_order_id;
        $userCard->lastupdate = $lastupdate;
        $userCard->disabled = $disabled;
        $userCard->insert();
    }

    /**
     * Возвращает массив объектов UserCard, карты с включенными рекурентными платежами
     * @param integer $user_id ид пользователя
     * @param string|bool $card карта которую надо искать, если пусто вернет все карты с рекурентными платежами
     * @return array object UserCard
     */
    public static function cardRecurrent(int $user_id, $card = false)
    {
        $cards = self::where('user_id', $user_id)->
                    whereNotNull('recurrent_order_id')->
                    where('disabled', false);

        if ($card) {
            $cards->where('card', 'LIKE', "%$card%");
        }

        return $cards->get();
    }

    /**
     * Ищет карту у пользователя, возвращает объект UserCard
     * @param integer $userId ид пользователя
     * @param string $card карта пользователя, например 430000XXXXXX0777
     * @return object UserCard
     */
    public static function searchCard($userId, $card)
    {
        $user_card = self::where([
            ['user_id', $userId],
            ['card', 'LIKE', "%$card%"]
        ])->first();

        return $user_card;
    }

    /**
     * Ищет карту по ид рекуретного платежа
     * @param integer $recurrentId ид платежа
     * @return object UserCard
     */
    public static function searchCardByRecurrentId($recurrentId)
    {
        $user_card = self::where('recurrent_order_id', $recurrentId);
        return $user_card->get();
    }

    /**
     * отменяет по карте рекурентные платежи
     */
    public function cardRecurrentCancel()
    {
        $this->recurrent_order_id = null;
        $this->save();
    }

    /**
     * изменяет статус видимости карты для пользователей
     */
    public function cardVisibleChange($value)
    {
        $this->disabled = $value;
        $this->update();
    }

    /**
     * вычисляет сумму по карте доступную для вывода пользователю
     */
    public function sumForCashout()
    {
        $cashoutArr = Logs_cashout::activeCashoutRequests($this->user_id, $this->card);
        $sum = $this->summ;
        foreach ($cashoutArr as $cashout) {
            $sum -= $cashout->initial_value;
        }
        return $sum;
    }

    /**
     * сумма доступная к выводу по всем картам пользователя, без учета активных заявок
     * @param integer $user_id ид пользователя
     * @return integer вернет сумму к выводу по всем картам
     * 
     * @todo поправить, не учтены выведенные средства
     */
    public static function sumCashout($userId)
    {
        $sumCashout = self::where([
            ['user_id', $userId],
            ['disabled', false]
        ])->sum('summ');

        return $sumCashout;
    }

    /**
     * Возвращает список карт на которые можно делать вывод средств
     * @param integer|bool $userId ид пользователя, если пусто или false будет взят текущий пользователь
     * @return array object
     */
    public static function getCardsForCashout($userId = false)
    {
        if (!$userId) {
            $userId = Auth::id();
        }

        $user_cards = self::where([
            ['user_id', $userId],
            ['disabled', false],
            ['sum', '>', 0]
        ]);

        return $user_cards->get();
    }

    /**
     * Возвращает список доступных карт
     * @param integer|bool $userId ид пользователя, если пусто или false будет взят текущий пользователь
     * @return array object
     */
    public static function getCards($userId = false)
    {
        if (!$userId) {
            $userId = Auth::id();
        }

        $user_cards = self::where([
            ['user_id', $userId],
            ['disabled', false],
        ]);

        return $user_cards->get();
    }

    /**
     * получение списка всех банковских карт владельца,
     * с которых юзер делал успешные пополнения
     * @param integer|bool $userId ид пользователя, если пусто или false будет взят текущий пользователь
     * @return array object
     */
    public static function getCardsAll($userId = false)
    {
        if (!$userId) {
            $userId = Auth::id();
        }

        $user_cards = self::where('user_id', $userId);

        return $user_cards->get();
    }

    /**
     * поиск совпадений карт
     * @param integer $userId ид пользователя
     * @param string $card карту которую будем искать
     * @param string $type тип платежной системы
     * @return array object
     */
    public static function searchDuplicate($userId, $card)
    {
        $user_cards = self::where([
            ['user_id', '!=',  $userId],
            ['card', 'LIKE', "%$card%"]
        ]);

        return $user_cards->get();
    }

    public function __toString()
    {
        return "{$this->id} {$this->user_id} {$this->card}";
    }




    /**
     * =========          релейшены          =========
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
}
