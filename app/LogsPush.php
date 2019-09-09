<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property int $status @todo возможно лишнее
 * @property int $type
 * @property string $title
 * @property string $message
 * @property string $created_at
 * @property string $updated_at
 * 
 * @todo скорее всего надо хранить всю переменную data
 */
class LogsPush extends Model
{
    protected $table = 'logs_push';

    protected $fillable = [
        'user_id',
        'status',
        'type',
        'data',
        'response'
    ];

    protected $casts = [
        'data' => 'array',
        'response' => 'array'
    ];

    const S_WAITING = 0;
    const S_SUCCESS = 1;
    const S_ERROR   = 2;

    const T_PRIVATE = 1;
    const T_PUBLIC  = 2;

    public function getDateFormat()
    {
        return 'U';
    }

    public function getDates()
    {
        return [];
    }

    protected $hidden = [
        'response'
    ];

    public static function historyForApp(User $user)
    {
        $logs = self::where('user_id', $user->id)->
            where('status', self::S_SUCCESS)->      /** @todo нужно ли? */ 
            orderBy('id', 'desc')->
            get();
        $logsArray = [];
        foreach ($logs as $log) {
            $logsArray[] = [
                'data'          =>  $log->data,
                'date'    =>  $log->created_at
            ];
        }
        return $logsArray;
    }

    public static function add(User $user, $type = self::T_PRIVATE, array $data = null)
    {
        return self::create([
            'user_id'   => $user->id,
            'status'    => self::S_WAITING,
            'type'      => self::T_PRIVATE,
            'data'      => $data,
        ]);
    }



    /**
     * =========          релейшены          =========
     */
    public function user()
    {
        return $this->hasOne('App\UserDevice', 'user_id', 'id');
    }
}
