<?php

namespace App\Http\Middleware;

use Closure;

class isVerified
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
        if ($request->user()->isVerified()) {
            return $next($request);
        } else {
            $answer['success'] = false;
            $answer['message'] = 'Вы должны пройти верификацию';
            return response()->json($answer, 403);
        }
    }
}
