<?php

namespace App;

use Auth;
use App\Components\Toolkit;

use Illuminate\Database\Eloquent\Model;

class LogsUser extends Model
{
    protected $table = 'logs_user';

    protected $fillable = [
        'user_id',
        'by_user_id',
        'property',
        'before',
        'after',
        'ip',
    ];

    public $timestamps = false;

    /**
     * @param User $user
     * @param string $property имя изменяемого атрибута
     * @param string $before значение атрибута до изменения
     */
    public static function add(User $user, array $log)
    {
        self::create([
            'user_id'       => $user->id,
            'by_user_id'    => Auth::user()->id,
            'property'      => $log['property'],
            'before'        => $log['before'],
            'after'         => $log['after'],
            'ip'            => Toolkit::getRealIp(),
        ]);
    }
}
