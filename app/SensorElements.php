<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 */
class SensorElements extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['id', 'name'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'vehicle-server';

}
