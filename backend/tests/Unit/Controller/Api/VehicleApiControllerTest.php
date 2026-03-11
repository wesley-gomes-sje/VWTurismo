<?php

namespace Tests\Unit\Controller\Api;

use App\Controller\Api\VehicleApiController;
use App\Model\Vehicle;

class VehicleApiControllerTest extends ApiTestCase
{
    private function makeController(array $body, ?Vehicle $vehicle = null): VehicleApiController
    {
        $vehicle = $vehicle ?? $this->createMock(Vehicle::class);

        return new class($body, $vehicle) extends VehicleApiController {
            public function __construct(private array $fakeBody, ?Vehicle $vehicle = null)
            {
                parent::__construct($vehicle);
            }
            protected function body(): array { return $this->fakeBody; }
        };
    }

    public function testIndexReturnsVehicleList(): void
    {
        $mock = $this->createMock(Vehicle::class);
        $mock->method('all')->willReturn([['id' => 1, 'brand' => 'VW', 'model' => 'Comfortline']]);

        $ctrl   = $this->makeController([], $mock);
        $result = $this->capture(fn() => $ctrl->index());

        $this->assertTrue($result['success']);
        $this->assertCount(1, $result['data']);
    }

    public function testStoreReturnsMissingFieldError(): void
    {
        $ctrl   = $this->makeController(['brand' => 'VW', 'model' => '', 'plate' => '', 'year' => '']);
        $result = $this->capture(fn() => $ctrl->store());

        $this->assertFalse($result['success']);
        $this->assertSame(422, http_response_code());
    }

    public function testStoreReturns201OnSuccess(): void
    {
        $mock = $this->createMock(Vehicle::class);
        $mock->method('register')->willReturn(true);

        $ctrl   = $this->makeController(['brand' => 'VW', 'model' => 'Comfortline', 'plate' => 'ABC1234', 'year' => '2023'], $mock);
        $result = $this->capture(fn() => $ctrl->store());

        $this->assertTrue($result['success']);
        $this->assertSame(201, http_response_code());
    }

    public function testStoreReturns500OnFailure(): void
    {
        $mock = $this->createMock(Vehicle::class);
        $mock->method('register')->willReturn(false);

        $ctrl   = $this->makeController(['brand' => 'VW', 'model' => 'X', 'plate' => 'X', 'year' => '2020'], $mock);
        $result = $this->capture(fn() => $ctrl->store());

        $this->assertFalse($result['success']);
        $this->assertSame(500, http_response_code());
    }
}
