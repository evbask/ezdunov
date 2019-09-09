<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * лог отправленных смс
 * 
 * @property int $id
 * @property string $sms_id
 * @property string $sms_text
 * @property string $sms_sender
 * @property string $sms_target
 * @property integer $sms_status
 * @property integer $sms_created
 * @property integer $sms_delivered
 * @property integer $created_at
 * @property integer $updated_at
 */
class LogsSms extends Model
{
    protected $table = 'sms_log';
    
    /**
     * @var array
     */
    protected $fillable = [
        'sms_id',
        'sms_text',
        'sms_sender',
        'sms_target',
        'sms_status',
        'sms_created',
        'sms_delivered'
    ];

    const S_WAITING = 0;
    const S_SUCCESS = 1;
    const S_ERROR   = 2;
}
