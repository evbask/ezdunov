<?php
namespace App;

use App\RentRequest;
use App\LogsRentStatus;

use Illuminate\Database\Eloquent\Model;

/**
 * модель самокатов
 * @property int $id
 * @property int $status
 * @property int $price
 */
class SrvVehicle extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'status',
        'city_id',
        'imei',
        'voltage',
        'problems',
        'type'
    ];
    protected $connection = 'vehicle-server';
    
    protected $table = 'vehicle';

    /**
     * свободен
     */
    const S_FREE        = 1;
    /**
     * аренда
     */
    const S_RENT        = 2;
    /**
     * бронь
     */
    const S_RESERVATION = 3;
    /**
     * какие то проблемы
     */
    const S_PROBLEM     = 4;

    /**
     * Названия статусов на русском
     */
    const S_NAMES = [
        self::S_FREE        => 'свободен',
        self::S_RENT        => 'аренда',
        self::S_RESERVATION => 'бронь',
        self::S_PROBLEM     => 'какие то проблемы',
    ];

    public function getTypeName()
    {
        return static::S_NAMES[$this->status];
    }

    /**
     * массив информации об устройстве для приложения
     * @return array
     */
    public function infoForApp()
    {
        $problem = [];
        $photos = $this->preRentPhoto->where('active', true);
        foreach ($photos as $photo) {
            $problem[] = $photo->id;
        }

        $tariffs = [];
        foreach ($this->tariff as $tariff) {
            $tariffs[] = [
                'id'    => $tariff->id,
                'type'  => $tariff->type_rent,
                'price' => $tariff->price,
                'name'  => $tariff->name,
            ];
        }
        $array = [
            'id'        =>  $this->id,
            'type'      =>  $this->type,
            'name'      =>  null,
            'status'    =>  $this->status,
            'city'      =>  $this->city,
            'voltage'   =>  $this->voltage,
            'lockKey'   =>  $this->lock_key,
            'mac'       =>  $this->mac,
            'problems'  =>  $problem,
            'tariffs'   =>  $tariffs,
        ];
        return $array; 
    }

    /**
     * получить количество свободных устройств
     */
    public static function getCountFree()
    {
        return self::where('status', self::S_FREE)->count() - RentRequest::getCountWaiting();
    }




    /**
     * =========          релейшены          =========
     */
    /**
     * получить тарифы для текущего вехикла
     */
    public function tariff()
    {
        $obj = $this->setConnection('pgsql')->hasMany('App\Tariff', 'type_vehicle', 'type')->
                    where('enable', true);
        $this->setConnection('vehicle-server');
        return $obj;
    }

    public function rent()
    {
        $obj = $this->setConnection('pgsql')->hasMany('App\Rent', 'vehicle_id', 'id');
        $this->setConnection('vehicle-server');
        return $obj;
    }

    public function preRentPhoto()
    {
        $obj = $this->setConnection('pgsql')->hasMany('App\PreRentPhoto', 'vehicle_id', 'id');
        $this->setConnection('vehicle-server');
        return $obj;
    }

    public function vehicleType()
    {
        return $this->hasOne('App\SrvVehicleTypes', 'id', 'type');
    }

    public function geoLog()
    {
        return $this->hasMany('App\SrvVehicle', 'vehicle_id', 'id');
    }

    public function sensorElements()
    {
        return $this->hasOne('App\SrvVehicleSensorElements', 'vehicle_id', 'id');
    }
}