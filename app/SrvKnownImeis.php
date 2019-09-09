<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $imei
 * @property int $added
 */
class SrvKnownImeis extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['id', 'imei', 'added'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'vehicle-server';

}
