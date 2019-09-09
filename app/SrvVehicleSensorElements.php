<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $element_id
 * @property int $size
 * @property int $value
 * @property int $vehicle_id
 */
class SrvVehicleSensorElements extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['id', 'element_id', 'size', 'value', 'vehicle_id'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'vehicle-server';

}
