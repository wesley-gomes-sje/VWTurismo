<?php

namespace Tests\Unit\Controller\Api;

use App\Controller\Api\RouteApiController;
use App\Model\Route;

class RouteApiControllerTest extends ApiTestCase
{
    private function makeController(array $body, ?Route $route = null): RouteApiController
    {
        $route = $route ?? $this->createMock(Route::class);

        return new class($body, $route) extends RouteApiController {
            public function __construct(private array $fakeBody, ?Route $route = null)
            {
                parent::__construct($route);
            }
            protected function body(): array { return $this->fakeBody; }
        };
    }

    public function testIndexReturnsRouteList(): void
    {
        $mock = $this->createMock(Route::class);
        $mock->method('all')->willReturn([['origin' => 'SP', 'destination' => 'RJ', 'distance' => 430]]);

        $ctrl   = $this->makeController([], $mock);
        $result = $this->capture(fn() => $ctrl->index());

        $this->assertTrue($result['success']);
        $this->assertCount(1, $result['data']);
    }

    public function testIndexReturnsEmptyArrayWhenNoRoutes(): void
    {
        $mock = $this->createMock(Route::class);
        $mock->method('all')->willReturn(false);

        $ctrl   = $this->makeController([], $mock);
        $result = $this->capture(fn() => $ctrl->index());

        $this->assertTrue($result['success']);
        $this->assertSame([], $result['data']);
    }

    public function testStoreReturnsMissingFieldError(): void
    {
        $ctrl   = $this->makeController(['origin' => '1', 'destination' => '', 'distance' => null]);
        $result = $this->capture(fn() => $ctrl->store());

        $this->assertFalse($result['success']);
        $this->assertSame(422, http_response_code());
    }

    public function testStoreReturns201OnSuccess(): void
    {
        $mock = $this->createMock(Route::class);
        $mock->method('register')->willReturn(true);

        $ctrl   = $this->makeController(['origin' => '1', 'destination' => '2', 'distance' => 430], $mock);
        $result = $this->capture(fn() => $ctrl->store());

        $this->assertTrue($result['success']);
        $this->assertSame(201, http_response_code());
    }

    public function testStoreReturns500OnFailure(): void
    {
        $mock = $this->createMock(Route::class);
        $mock->method('register')->willReturn(false);

        $ctrl   = $this->makeController(['origin' => '1', 'destination' => '2', 'distance' => 100], $mock);
        $result = $this->capture(fn() => $ctrl->store());

        $this->assertFalse($result['success']);
        $this->assertSame(500, http_response_code());
    }
}
