<?php

namespace Tests\Unit\Services;

use App\Services\JwtService;
use PHPUnit\Framework\TestCase;

class JwtServiceTest extends TestCase
{
    public function testGenerateReturnsNonEmptyString(): void
    {
        $token = JwtService::generate(['sub' => 1]);
        $this->assertIsString($token);
        $this->assertNotEmpty($token);
    }

    public function testGeneratedTokenHasThreeParts(): void
    {
        $token = JwtService::generate(['sub' => 1]);
        $this->assertCount(3, explode('.', $token));
    }

    public function testValidateReturnsPayloadForValidToken(): void
    {
        $token   = JwtService::generate(['sub' => 42, 'profile' => 'admin']);
        $payload = JwtService::validate($token);

        $this->assertIsArray($payload);
        $this->assertSame(42, $payload['sub']);
        $this->assertSame('admin', $payload['profile']);
    }

    public function testValidateReturnsFalseForInvalidToken(): void
    {
        $result = JwtService::validate('token.invalido.aqui');
        $this->assertFalse($result);
    }

    public function testValidateReturnsFalseForEmptyString(): void
    {
        $this->assertFalse(JwtService::validate(''));
    }

    public function testValidateReturnsFalseForTamperedToken(): void
    {
        $token  = JwtService::generate(['sub' => 1]);
        $parts  = explode('.', $token);
        $parts[1] = base64_encode('{"sub":999}');
        $tampered = implode('.', $parts);

        $this->assertFalse(JwtService::validate($tampered));
    }

    public function testPayloadContainsIatAndExp(): void
    {
        $before  = time();
        $token   = JwtService::generate(['sub' => 1]);
        $payload = JwtService::validate($token);

        $this->assertArrayHasKey('iat', $payload);
        $this->assertArrayHasKey('exp', $payload);
        $this->assertGreaterThanOrEqual($before, $payload['iat']);
        $this->assertGreaterThan($payload['iat'], $payload['exp']);
    }
}
