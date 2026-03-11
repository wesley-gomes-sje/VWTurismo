<?php

namespace Tests\Unit\Controller\Api;

use App\Controller\Api\UserApiController;
use App\Model\User;

class UserApiControllerTest extends ApiTestCase
{
    private function makeController(?User $user = null): UserApiController
    {
        $user = $user ?? $this->createMock(User::class);

        return new class($user) extends UserApiController {
            public function __construct(?User $user = null)
            {
                parent::__construct($user);
            }
        };
    }

    public function testIndexReturnsCustomerList(): void
    {
        $mock = $this->createMock(User::class);
        $mock->method('showCustomers')->willReturn([
            ['name' => 'Ana', 'email' => 'ana@test.com'],
        ]);

        $ctrl   = $this->makeController($mock);
        $result = $this->capture(fn() => $ctrl->index());

        $this->assertTrue($result['success']);
        $this->assertCount(1, $result['data']);
    }

    public function testShowReturns404WhenUserNotFound(): void
    {
        $mock = $this->createMock(User::class);
        $mock->method('all')->willReturn([['id' => 2, 'name' => 'Bob']]);

        $ctrl   = $this->makeController($mock);
        $result = $this->capture(fn() => $ctrl->show('99'));

        $this->assertFalse($result['success']);
        $this->assertSame(404, http_response_code());
    }

    public function testShowReturnsUserWhenFound(): void
    {
        $mock = $this->createMock(User::class);
        $mock->method('all')->willReturn([['id' => 1, 'name' => 'Ana', 'email' => 'ana@test.com']]);

        $ctrl   = $this->makeController($mock);
        $result = $this->capture(fn() => $ctrl->show('1'));

        $this->assertTrue($result['success']);
        $this->assertSame('Ana', $result['data']['name']);
    }
}
