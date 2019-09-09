<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class LogsSession extends Model{
    
    const CREATED_AT = 'created';
    //const UPDATED_AT = 'updated';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'session_id',
        'log_data',
        'log_message',
    ];
    protected $casts = [
        'log_data' => 'array'
    ];
    protected $table = 'logs_session';

    public static function newLog($data, $message){
        $session_log = LogsSession::create([
            'session_id' => session()->getId(),
            'log_data' => $data,
            'log_message' => $message
        ]);
        $session_log->save();
        return $session_log;
    }
}