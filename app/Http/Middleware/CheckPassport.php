<?php

namespace App\Http\Middleware;

use Closure;

class CheckPassport
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

        if ($passport->request_status != 2 && !$user->checkGroup('staff')) {

            return redirect('/home');
        }

        return $next($request);
    }
}
