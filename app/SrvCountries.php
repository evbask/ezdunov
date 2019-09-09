<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 */
class SrvCountries extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'country';

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
