<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtService
{
    private static function secret(): string
    {
        return $_ENV['JWT_SECRET'] ?? 'vwturismo-secret-key';
    }

    private static function expiration(): int
    {
        return (int) ($_ENV['JWT_EXPIRATION'] ?? 3600);
    }

    public static function generate(array $payload): string
    {
        $payload['iat'] = time();
        $payload['exp'] = time() + self::expiration();

        return JWT::encode($payload, self::secret(), 'HS256');
    }

    public static function validate(string $token): array|false
    {
        try {
            $decoded = JWT::decode($token, new Key(self::secret(), 'HS256'));
            return (array) $decoded;
        } catch (\Exception) {
            return false;
        }
    }
}
