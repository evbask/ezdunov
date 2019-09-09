<?php namespace App\Http\Middleware;

use Closure;
use Session;
use App;
use Config;

class Locale {

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
        if($request->has('lang')) {
            $language_to_set = $request->input('lang');
            if(in_array( $language_to_set, $this->supported_locales) ){
                setcookie('locale', $language_to_set, time()+60*60*24*30);
            } else {
                unset($language_to_set);
            }
        }
        $language = $_COOKIE['locale'] ?? Config::get('app.locale');
        $language = $language_to_set ?? $language;
        App::setLocale($language);
        try {
            if (isset($_SERVER['HTTP_USER_AGENT'])) {
                $browser = get_browser(null, true);
            } else {
                $browser =['none'];
            }
        } catch(Exception $e){
            $browser =['none'];
        }
        session(['browser' => $browser]);

        return $next($request);
    }

}