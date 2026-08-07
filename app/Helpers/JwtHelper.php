<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtHelper
{
    public static function validateToken($token)
    {
        try {
            return $payload = JWT::decode(
                $token,
                new Key(config('services.gojags.jwt_secret'), 'HS256')
            );

            if (
                empty($payload->exp) ||
                empty($payload->iss) ||
                empty($payload->aud)
            ) {
                throw new \UnexpectedValueException('JWT claims tidak lengkap.');
            }

            if ($payload->iss !== config('services.gojags.issuer')) {
                throw new \UnexpectedValueException('JWT issuer tidak valid.');
            }

            if ($payload->aud !== config('services.gojags.audience')) {
                throw new \UnexpectedValueException('JWT audience tidak valid.');
            }
        } catch (\Exception $e) {
            return false; // Invalid token
        }
    }
}
