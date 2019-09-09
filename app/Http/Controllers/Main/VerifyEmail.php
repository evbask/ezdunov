<?php

namespace App\Http\Controllers\Main;

use App\User;
use App\Components\Toolkit;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class VerifyEmail extends Controller 
{
    public function activation($userId, $token)
    {
        $user = User::findOrFail($userId);

        if (!$user->email_verified_at) {
            // проверка токена
            if (Toolkit::getVerifyEmailToken($user) == $token) {
                $user->email_verified_at = date("Y-m-d H:i:s");
                $user->save();
                /**
                 * @todo добавить вьюху об успешной проверке
                 */
                return redirect('/');
            } else {
                return "Что то пошло не так";
            }
        }
        return redirect('/');
    }
}
