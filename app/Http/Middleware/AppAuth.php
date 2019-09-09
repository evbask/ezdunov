<?php

namespace App\Http\Middleware;

use \App;
use Auth;
use Closure;
use \Exception;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\Carbon;

class AppAuth extends Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        try {
            $this->authenticate($request, $guards);
        } catch (AuthenticationException $e) {
            return response()->json(['succcess' => false, 'message' => 'Вы не авторизованы.'], 401);
        } catch (Exception $e) {
            throw $e;
        }

        if (Auth::check()) {
            $user = Auth::user();
            App::setLocale($user->getLocale());
            $user->last_activity_app = Carbon::now();
            $user->save();
        }
        
        return $next($request);
    }
}
