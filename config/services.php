<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    'gojags' => [
        'url' => env('APP_GOJAGS_URL'),
        'client_uuid' => env('PAPS_UUID'),
        'jwt_secret' => env('JWT_SECRET'),
        'issuer' => env('GOJAGS_JWT_ISSUER'),
        'audience' => env('GOJAGS_JWT_AUDIENCE', 'paps'),
    ],

    // API integrasi pelatihan PAPS -> GoJAGS (lihat dokumen_md/API_PAPS.md)
    'paps' => [
        'base_url' => env('PAPS_BASE_URL', 'https://gojags.web.bps.go.id/server/api/v1/paps'),
        'client_id' => env('PAPS_CLIENT_ID'),
        'client_secret' => env('PAPS_CLIENT_SECRET'),
        'timeout' => env('PAPS_TIMEOUT', 30),
    ],

];
