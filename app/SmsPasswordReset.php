<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * смена пароля с помощью смс кода
 * 
 * @param integer $id
 * @param integer $user_id пользователь
 * @param string $phone телефон
 * @param string $code код для смены пароля
 * @param boolean $success использован или нет
 * @param integer $updated_at
 * @param integer $created_at
 */
class SmsPasswordReset extends Model
{
    protected $table = 'sms_password_reset';

    protected $fillable = [
        'user_id',
        'phone',
        'code',
        'success',
    ];
}
