<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * история логов
 * 
 * @property int $id
 * @property string $promo_code
 * @property int $type
 * @property int $discount
 * @property string $start_time
 * @property string $end_time
 * @property string $created_at
 * @property string $updated_at
 * @property Rent[] $rents
 * @property PromocodesLog[] $promocodesLogs
 */
class Promocode extends Model
{

    const T_PRIVATE = 1;

    const P_TYPES = [
        self::T_PRIVATE => 'privateHandler'
    ];

    const P_TYPE_NAMES = [
        self::T_PRIVATE => "Личный"
    ];
    /**
     * @var array
     */
    protected $fillable = [
        'promo_code', 
        'type', 
        'discount', 
        'start_time', 
        'end_time',
    ];

    /**
     * вычислит сумму скидки по текущему промокоду
     * @return integer
     */
    public function getDiscount(Rent $rent)
    {
        return 0;
        $function_name = self::P_TYPES[$this->type];

        $discount = $this->$function_name($rent);
        return $discount;
    }

    /**
     * Вернет уникальный личный промокод
     * @param integer $length
     * @return string
     */
    public static function generatePromocode(int $length = 3)
    {
        while(true) {
            $randomletter = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
            $randomnumber = substr(str_shuffle("0123456789"), 0, $length);
            $promocode = str_shuffle($randomletter . $randomnumber);
            if (!self::where('promo_code', $promocode)->exists()) {
                break;
            }
        }
        return $promocode;
    }

    /**
     * =========       Обработчики промокодов =======
     */

    /**
     * Обработчик скидки по личному промокоду (10% на старте)
     *
     * @param Rent $rent
     * @return int
     */
    private function privateHandler(Rent $rent) {
        $percent = 10;

        $rent_payment = $rent->paymentAmount;
        $discount = $rent_payment*$percent/100;

        return $discount;
    }

    /** ============================================== */

    /**
     * =========          релейшены          =========
     */
    public function rents()
    {
        return $this->hasMany('App\Rent');
    }

    public function promocodesLogs()
    {
        return $this->hasMany('App\PromocodesLog', 'promocode_id', 'id');
    }
}
