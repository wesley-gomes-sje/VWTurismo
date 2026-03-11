<?php

namespace Tests\Unit\Middleware;

use App\Middleware\JwtMiddleware;
use App\Services\JwtService;
use PHPUnit\Framework\TestCase;

class JwtMiddlewareTest extends TestCase
{
    protected function tearDown(): void
    {
        unset($_SERVER['HTTP_AUTHORIZATION']);
    }

    private function setToken(array $payload): string
    {
        $token = JwtService::generate($payload);
        $_SERVER['HTTP_AUTHORIZATION'] = "Bearer $token";
        return $token;
    }

    // --- getToken ---

    public function testGetTokenReturnsFalseWhenHeaderAbsent(): void
    {
        unset($_SERVER['HTTP_AUTHORIZATION']);
        $this->assertFalse(JwtMiddleware::getToken());
    }

    public function testGetTokenExtractsBearerToken(): void
    {
        $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer abc.def.ghi';
        $this->assertSame('abc.def.ghi', JwtMiddleware::getToken());
    }

    public function testGetTokenReturnsFalseForNonBearerHeader(): void
    {
        $_SERVER['HTTP_AUTHORIZATION'] = 'Basic dXNlcjpwYXNz';
        $this->assertFalse(JwtMiddleware::getToken());
    }

    // --- check ---

    public function testCheckReturnsFalseWithNoToken(): void
    {
        unset($_SERVER['HTTP_AUTHORIZATION']);
        $this->assertFalse(JwtMiddleware::check());
    }

    public function testCheckReturnsTrueWithValidToken(): void
    {
        $this->setToken(['sub' => 1, 'profile' => 'user']);
        $this->assertTrue(JwtMiddleware::check());
    }

    public function testCheckReturnsFalseWithInvalidToken(): void
    {
        $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer token.invalido.aqui';
        $this->assertFalse(JwtMiddleware::check());
    }

    public function testCheckReturnsTrueForMatchingProfile(): void
    {
        $this->setToken(['sub' => 1, 'profile' => 'admin']);
        $this->assertTrue(JwtMiddleware::check('admin'));
    }

    public function testCheckReturnsFalseForWrongProfile(): void
    {
        $this->setToken(['sub' => 1, 'profile' => 'user']);
        $this->assertFalse(JwtMiddleware::check('admin'));
    }

    // --- payload ---

    public function testPayloadReturnsArrayForValidToken(): void
    {
        $this->setToken(['sub' => 7, 'profile' => 'admin', 'name' => 'Wesley']);
        $payload = JwtMiddleware::payload();

        $this->assertIsArray($payload);
        $this->assertSame(7, $payload['sub']);
        $this->assertSame('admin', $payload['profile']);
    }

    public function testPayloadReturnsFalseWithNoToken(): void
    {
        unset($_SERVER['HTTP_AUTHORIZATION']);
        $this->assertFalse(JwtMiddleware::payload());
    }

    // --- protect ---

    public function testProtectReturns401WhenNotAuthenticated(): void
    {
        unset($_SERVER['HTTP_AUTHORIZATION']);
        $handler = JwtMiddleware::protect(function () {
            echo 'ok';
        });

        ob_start();
        $handler();
        $output = ob_get_clean();

        $decoded = json_decode($output, true);
        $this->assertFalse($decoded['success']);
        $this->assertSame(401, http_response_code());
    }

    public function testProtectCallsHandlerWhenAuthenticated(): void
    {
        $this->setToken(['sub' => 1, 'profile' => 'user']);
        $called  = false;
        $handler = JwtMiddleware::protect(function () use (&$called) {
            $called = true;
        });

        ob_start();
        $handler();
        ob_end_clean();

        $this->assertTrue($called);
    }

    public function testProtectReturns401ForWrongProfile(): void
    {
        $this->setToken(['sub' => 1, 'profile' => 'user']);
        $handler = JwtMiddleware::protect(function () {
            echo 'ok';
        }, 'admin');

        ob_start();
        $handler();
        $output = ob_get_clean();

        $decoded = json_decode($output, true);
        $this->assertFalse($decoded['success']);
    }
}
