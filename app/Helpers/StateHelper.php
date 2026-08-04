<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class StateHelper
{
    private const SESSION_KEY = 'gojags_sso_state';

    /**
     * Generate a random state string and store it in session.
     *
     * @return string
     */
    public static function generateState()
    {
        $state = Str::random(32);
        Session::put(self::SESSION_KEY, $state);
        Session::save();

        return $state;
    }

    /**
     * Get the stored state from session.
     *
     * @return string|null
     */
    public static function getState()
    {
        return Session::get(self::SESSION_KEY);
    }

    /**
     * Clear the stored state from session.
     *
     * @return void
     */
    public static function clearState()
    {
        Session::forget(self::SESSION_KEY);
    }
}
