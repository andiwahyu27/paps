<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Membatasi percobaan login (POST /login) per IP.
 *
 * Login PAPS memakai Auth::routes() yang tidak punya throttle bawaan.
 * Counter dihapus saat login berhasil, dan dinaikkan saat gagal, supaya
 * pengguna sah tidak ikut terkunci karena login sukses berulang.
 */
class ThrottleLogin
{
    /** Jumlah percobaan gagal yang diizinkan. */
    private const MAX_ATTEMPTS = 5;

    /** Jendela waktu (detik) sebelum counter direset. */
    private const DECAY_SECONDS = 300;

    public function handle(Request $request, Closure $next)
    {
        if (! $this->isLoginAttempt($request)) {
            return $next($request);
        }

        $key = 'login-attempt:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($key);

            abort(429, 'Terlalu banyak percobaan login. Coba lagi dalam ' . $seconds . ' detik.');
        }

        $response = $next($request);

        if (Auth::check()) {
            RateLimiter::clear($key);
        } else {
            RateLimiter::hit($key, self::DECAY_SECONDS);
        }

        return $response;
    }

    private function isLoginAttempt(Request $request): bool
    {
        return $request->isMethod('post') && $request->is('login');
    }
}
