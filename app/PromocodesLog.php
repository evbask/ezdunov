<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $promocode_id
 * @property int $rent_id
 * @property int $user_id
 * @property string $created_at
 * @property string $updated_at
 * @property Promocode $promocode
 * @property Rent $rent
 * @property User $user
 */
class PromocodesLog extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'promocodes_log';

    /**
     * @var array
     */
    protected $fillable = [
        'promocode_id', 
        'rent_id', 
        'user_id', 
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function promocode()
    {
        return $this->belongsTo('App\Promocode');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rent()
    {
        return $this->belongsTo('App\Rent');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
