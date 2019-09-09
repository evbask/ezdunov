<?php namespace App\Http\Middleware;

use Closure;
use Session;
use App;
use Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class LastActivitySite {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    protected $supported_locales = [
        'ru',
        'en'
    ];
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $user->last_activity_site = Carbon::now();
            $user->save();
        }
        
        return $next($request);
    }

}