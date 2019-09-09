<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $vehicle_id
 * @property float $longitude
 * @property float $latitude
 * @property float $altitude
 * @property int $time
 */
class SrvGeoLog extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'geolog';

    /**
     * @var array
     */
    protected $fillable = ['id', 'vehicle_id', 'longitude', 'latitude', 'altitude', 'time'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'vehicle-server';

}
