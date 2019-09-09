<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $pre_rent_problem_id
 * @property string $photo_name
 * @property string $created_at
 * @property string $updated_at
 * @property PreRentProblem $preRentProblem
 */
class PreRentPhoto extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'photo_name',
        'active',
        'rent_id',
        'vehicle_id'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function preRentProblem()
    {
        return $this->belongsTo('App\PreRentProblem');
    }
}
