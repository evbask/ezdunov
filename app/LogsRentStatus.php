<?php

/**
 * @property integer $id
 * @property integer $rent_id
 * @property integer $user_id
 * @property integer $status
 * @property int $date
 */
namespace App;
use Illuminate\Database\Eloquent\Model;

class LogsRentStatus extends Model 
{
    protected $table = 'logs_rent_status';

    protected $fillable = [
        'rent_id',
        'user_id',
        'status',
        'date',
    ];

    public $timestamps = false;
}