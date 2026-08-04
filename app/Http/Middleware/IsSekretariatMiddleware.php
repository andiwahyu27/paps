<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class IsSekretariatMiddleware
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
        if (Auth::user()->role == 2) {
            return $next($request);
        } else {
            return abort(503, 'Anda tidak memiliki hak akses');
        }
    }
}
