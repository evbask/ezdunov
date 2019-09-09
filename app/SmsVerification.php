<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class SmsVerification extends Model{

    protected $fillable = [
        'user_id',
        'phone',
        'sms_code',
        'done_time',
        'done'
    ];
    
    protected $table = 'sms_verifications'; 
}