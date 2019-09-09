<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $type
 * @property int $price
 * @property string $name
 * @property string $created_at
 * @property string $updated_at
 * @property Rent[] $rents
 */
class Tariff extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'type', 
        'rent_type',
        'payment_type',
        'price', 
        'name', 
    ];

    protected $table = 'tariffs';

    /**
     * тип оплаты
     */
    const T_PRE = 1;
    const T_POST = 2;


    const E_ENABLE = true;
    const E_DISABLE = false;

    /**
     * получить сумму к оплате по тарифу
     * @param integer $tariff тариф в момент взятия
     * @todo end time
     */
    // public function getSumPayment(Rent $rent)
    // {
    //     switch ($i) {
    //         case self::T_PRE:
    //             return $rent->price * 
    //             break;
    //         case self::T_POST:
    //             echo "i равно 1";
    //             break;
    //         default:
    //             throw new Exception('Тип оплаты не существует');
    //     }
    // }




    /**
     * =========          релейшены          =========
     */

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rents()
    {
        return $this->hasMany('App\Rent');
    }
}
