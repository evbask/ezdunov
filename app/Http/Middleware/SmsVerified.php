<?php

namespace App\Http\Middleware;

use Closure;

class SmsVerified
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
        if(!$user){
            return redirect('/login');
        }
        if(!$user->sms_verified && !$user->checkGroup('staff')){
            return redirect('/sms_verify');
        }

        return $next($request);
    }
}
