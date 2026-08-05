<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class StateHelper
{
    private const COOKIE_NAME = 'gojags_sso_state';

    /**
     * Generate a random state string and store it in a dedicated cookie.
     *
     * @return string
     */
    public static function generateState()
    {
        $state = Str::random(32);

        // Simpan state di cookie tersendiri agar tetap tersedia saat callback
        // dari provider SSO, terlepas dari session cookie domain.
        $cookie = Cookie::make(
            self::COOKIE_NAME,
            $state,
            10, // minutes
            '/',
            config('session.domain'),
            request()->isSecure(),
            true, // httponly
            false,
            'lax'
        );

        Cookie::queue($cookie);

        return $state;
    }

    /**
     * Get the stored state from the dedicated cookie.
     *
     * @return string|null
     */
    public static function getState()
    {
        return Cookie::get(self::COOKIE_NAME);
    }

    /**
     * Clear the stored state cookie.
     *
     * @return void
     */
    public static function clearState()
    {
        Cookie::queue(Cookie::forget(self::COOKIE_NAME));
    }
}
