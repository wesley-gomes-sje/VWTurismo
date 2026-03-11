<?php

namespace Tests\Unit\Controller\Api;

use App\Services\JwtService;
use PHPUnit\Framework\TestCase;

/**
 * Base para testes de API controllers.
 * Fornece helpers para simular body JSON e capturar output.
 */
abstract class ApiTestCase extends TestCase
{
    protected function tearDown(): void
    {
        unset($_SERVER['HTTP_AUTHORIZATION']);
    }

    /** Autentica como usuário com o perfil informado. */
    protected function authenticate(string $profile = 'user', int $sub = 1): void
    {
        $token = JwtService::generate(['sub' => $sub, 'profile' => $profile, 'name' => 'Test']);
        $_SERVER['HTTP_AUTHORIZATION'] = "Bearer $token";
    }

    /** Executa um callable capturando o output e retorna o JSON decodificado. */
    protected function capture(callable $fn): array
    {
        ob_start();
        $fn();
        $raw = ob_get_clean();
        return json_decode($raw, true) ?? [];
    }
}
