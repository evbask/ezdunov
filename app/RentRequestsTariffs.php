<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id ид
 * @property int $name название тарифа
 * @property int $price цена тарифа
 * @property bool $active флаг, активен ли тариф?
 * @property string $created_at
 * @property string $updated_at
 */
class RentRequestsTariffs extends Model
{
    protected $table = 'rent_requests_tariffs';
    private static $default = 1500;

    public static function getTariff(){
        $tariff = self::where('active', true)->first() ?? false;
        if($tariff){
            return $tariff['price'];
        }else{
            return self::$default;
        }
    }
}
