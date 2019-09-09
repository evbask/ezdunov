<?php

namespace App\Http\Middleware;

use Closure;
use App\PasportVerifyRequest;

class PassportVerified
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

        if (!PasportVerifyRequest::where('user_id', '=', $user->id)->exists() && !$user->checkGroup('staff')) {

            return redirect('/passport_verify');
        }

        return $next($request);
    }
}
