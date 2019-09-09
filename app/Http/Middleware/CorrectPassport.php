<?php

namespace App\Http\Middleware;

use App\PasportVerifyRequest;
use Closure;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CorrectPassport
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user =  $request->user();
        $passport = $user->passport;

        if(!$user){
            return redirect('/login');
        }

        if ($passport && !$user->checkGroup('staff')) {
            switch ($passport->request_status){
                case 1:
                    break;
                case 2:
                    return redirect('/edit_passport');
                break;
                case 3:
                    $this->makeUserNotified($user,3);
                break;
                case 4:
                    $this->makeUserNotified($user,4);

            }
        }

        return $next($request);
    }

    public function makeUserNotified($user,$status){
        $settings = $user->settings;
        if(!isset($settings['notified']) || !$settings['notified']){
            $settings['notified'] = true;
            $user->settings = $settings;
            $user->save();
            $this->showNotification('Ваша заявка на верификацию паспорта '.PasportVerifyRequest::T_STRINGS[$status]);
        }
    }

    // @TODO пока так, потом исправлю, конечно, перенесу в другое место
    public function showNotification($text){
        echo '<script type="text/javascript">
            alert("'.$text.'");
        </script>';
    }

}
