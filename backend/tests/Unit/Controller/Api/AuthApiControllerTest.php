<?php

namespace Tests\Unit\Controller\Api;

use App\Controller\Api\AuthApiController;
use App\Model\Login;
use App\Model\User;

class AuthApiControllerTest extends ApiTestCase
{
    private function makeController(array $body, ?Login $login = null, ?User $user = null): AuthApiController
    {
        $login = $login ?? $this->createMock(Login::class);
        $user  = $user  ?? $this->createMock(User::class);

        return new class($body, $login, $user) extends AuthApiController {
            public function __construct(
                private array  $fakeBody,
                ?Login         $login = null,
                ?User          $user  = null
            ) {
                parent::__construct($login, $user);
            }
            protected function body(): array { return $this->fakeBody; }
        };
    }

    // --- login ---

    public function testLoginReturnsMissingFieldsError(): void
    {
        $ctrl   = $this->makeController(['email' => '', 'password' => '']);
        $result = $this->capture(fn() => $ctrl->login());

        $this->assertFalse($result['success']);
        $this->assertSame(422, http_response_code());
    }

    public function testLoginReturnsUnauthorizedForInvalidCredentials(): void
    {
        $loginMock = $this->createMock(Login::class);
        $loginMock->method('login')->willReturn(false);

        $ctrl   = $this->makeController(['email' => 'a@b.com', 'password' => '123'], $loginMock);
        $result = $this->capture(fn() => $ctrl->login());

        $this->assertFalse($result['success']);
        $this->assertSame(401, http_response_code());
    }

    public function testLoginReturnsTokenOnSuccess(): void
    {
        $loginMock = $this->createMock(Login::class);
        $loginMock->method('login')->willReturn([
            'id' => 1, 'name' => 'Wesley', 'email' => 'w@b.com', 'profile' => 'admin',
        ]);

        $ctrl   = $this->makeController(['email' => 'w@b.com', 'password' => '123'], $loginMock);
        $result = $this->capture(fn() => $ctrl->login());

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('token', $result['data']);
        $this->assertArrayHasKey('user', $result['data']);
        $this->assertSame(200, http_response_code());
    }

    // --- register ---

    public function testRegisterReturnsMissingFieldsError(): void
    {
        $ctrl   = $this->makeController(['name' => '', 'email' => '', 'password' => '']);
        $result = $this->capture(fn() => $ctrl->register());

        $this->assertFalse($result['success']);
        $this->assertSame(422, http_response_code());
    }

    public function testRegisterReturnsInvalidEmailError(): void
    {
        $ctrl   = $this->makeController(['name' => 'W', 'email' => 'not-an-email', 'password' => '123']);
        $result = $this->capture(fn() => $ctrl->register());

        $this->assertFalse($result['success']);
        $this->assertSame(422, http_response_code());
    }

    public function testRegisterReturnsDuplicateEmailError(): void
    {
        $userMock = $this->createMock(User::class);
        $userMock->method('show')->willReturn(true);

        $ctrl   = $this->makeController(['name' => 'W', 'email' => 'w@b.com', 'password' => '123'], null, $userMock);
        $result = $this->capture(fn() => $ctrl->register());

        $this->assertFalse($result['success']);
        $this->assertSame(409, http_response_code());
    }

    public function testRegisterReturns201OnSuccess(): void
    {
        $userMock = $this->createMock(User::class);
        $userMock->method('show')->willReturn(false);
        $userMock->method('register')->willReturn(true);

        $ctrl   = $this->makeController(['name' => 'Wesley', 'email' => 'w@b.com', 'password' => '123'], null, $userMock);
        $result = $this->capture(fn() => $ctrl->register());

        $this->assertTrue($result['success']);
        $this->assertSame(201, http_response_code());
    }
}
