<?php

namespace App\Middleware;

use App\Services\JwtService;

class JwtMiddleware
{
    public static function getToken(): string|false
    {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (preg_match('/Bearer\s+(.+)/i', $header, $matches)) {
            return $matches[1];
        }
        return false;
    }

    public static function payload(): array|false
    {
        $token = self::getToken();
        if (!$token) return false;
        return JwtService::validate($token);
    }

    public static function check(?string $requiredProfile = null): bool
    {
        $payload = self::payload();
        if (!$payload) return false;

        if ($requiredProfile && ($payload['profile'] ?? '') !== $requiredProfile) {
            return false;
        }

        return true;
    }

    public static function protect(callable|array $handler, ?string $requiredProfile = null): callable
    {
        return function () use ($handler, $requiredProfile) {
            if (!self::check($requiredProfile)) {
                http_response_code(401);
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Não autorizado']);
                return;
            }

            if (is_array($handler)) {
                [$class, $method] = $handler;
                (new $class())->$method(...func_get_args());
            } else {
                $handler(...func_get_args());
            }
        };
    }
}
