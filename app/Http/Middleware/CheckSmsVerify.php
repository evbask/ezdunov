<?php

namespace App\Http\Middleware;

use Closure;

class CheckSmsVerify
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
        if($user->sms_verified && !$user->checkGroup('staff')){
            return redirect('/home');
        }
        return $next($request);
    }
}
