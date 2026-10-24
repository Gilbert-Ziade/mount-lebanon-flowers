<?php
declare(strict_types=1);

namespace App\Security\JWT;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/** The only two JWT operations the site needs: issue one, validate one. */
class AuthTokenService
{
    public const COOKIE   = 'auth_token';
    public const LIFETIME = 86400; // 24 hours, in seconds

    /** Creates a signed JWT for a user. It expires 24h after being issued. */
    public static function issue(int $userId): string
    {
        $now = time();

        return JWT::encode([
            'iat' => $now,
            'exp' => $now + self::LIFETIME,
            'sub' => (string) $userId,
        ], (string) config('jwt_secret'), 'HS256');
    }

    /** Returns the payload, or null when the token is missing, tampered with or expired. */
    public static function validate(?string $token): ?array
    {
        if ($token === null || $token === '') {
            return null;
        }

        try {
            return (array) JWT::decode($token, new Key((string) config('jwt_secret'), 'HS256'));
        } catch (\Throwable) {
            return null;
        }
    }
}