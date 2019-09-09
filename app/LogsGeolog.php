<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * гео логи пользователей
 * 
 * @property integer $id
 * @property integer $user_id
 * @property float $lat
 * @property float $lng
 * @property integer $date
 */
class LogsGeolog extends Model 
{
    protected $table = 'logs_geolog';

    protected $fillable = [
        'user_id',
        'lat',
        'lng',
        'date'
    ];

    public $timestamps = false;
}