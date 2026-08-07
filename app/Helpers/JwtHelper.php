<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtHelper
{
    public static function validateToken($token)
    {
        try {
            $secret = config('services.gojags.jwt_secret');
            if (!is_string($secret) || trim($secret) === '') {
                throw new \UnexpectedValueException('JWT secret belum dikonfigurasi.');
            }

            $payload = JWT::decode($token, new Key($secret, 'HS256'));

            if (empty($payload->exp) || empty($payload->iss)) {
                throw new \UnexpectedValueException('JWT claims tidak lengkap.');
            }
            if ($payload->iss !== config('services.gojags.issuer')) {
                throw new \UnexpectedValueException('JWT issuer tidak valid.');
            }
            // GOJAGS may omit aud; validate it only when present.
            if (isset($payload->aud) && $payload->aud !== config('services.gojags.audience')) {
                throw new \UnexpectedValueException('JWT audience tidak valid.');
            }

            return $payload;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
