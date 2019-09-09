<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * фото завершения аренды
 */
class PhotosCompletedRent extends Model
{
    protected $table = 'photos_completed_rent';

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'rent_id',
        'vehicle_id',
        'photo',
        'gps'
    ];

    protected $casts = [
        'gps' => 'array'
    ];

    public $timestamps = false;


    

    /**
     * =========          релейшены          =========
     */
}
