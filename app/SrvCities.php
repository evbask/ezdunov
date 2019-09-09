<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $country_id
 * @property string $name
 */
class SrvCities extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'city';

    /**
     * @var array
     */
    protected $fillable = ['id', 'country_id', 'name'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'vehicle-server';

}
