<?php

namespace Tests\Unit\Controller\Api;

use App\Controller\Api\CityApiController;
use App\Model\City;

class CityApiControllerTest extends ApiTestCase
{
    private function makeController(array $body, ?City $city = null): CityApiController
    {
        $city = $city ?? $this->createMock(City::class);

        return new class($body, $city) extends CityApiController {
            public function __construct(private array $fakeBody, ?City $city = null)
            {
                parent::__construct($city);
            }
            protected function body(): array { return $this->fakeBody; }
        };
    }

    // --- index ---

    public function testIndexReturnsCityList(): void
    {
        $cityMock = $this->createMock(City::class);
        $cityMock->method('all')->willReturn([['id' => 1, 'name' => 'São Paulo']]);

        $ctrl   = $this->makeController([], $cityMock);
        $result = $this->capture(fn() => $ctrl->index());

        $this->assertTrue($result['success']);
        $this->assertCount(1, $result['data']);
    }

    // --- store ---

    public function testStoreReturnsMissingFieldError(): void
    {
        $ctrl   = $this->makeController(['name' => '']);
        $result = $this->capture(fn() => $ctrl->store());

        $this->assertFalse($result['success']);
        $this->assertSame(422, http_response_code());
    }

    public function testStoreReturns201OnSuccess(): void
    {
        $cityMock = $this->createMock(City::class);
        $cityMock->method('register')->willReturn(true);

        $ctrl   = $this->makeController(['name' => 'Campinas'], $cityMock);
        $result = $this->capture(fn() => $ctrl->store());

        $this->assertTrue($result['success']);
        $this->assertSame(201, http_response_code());
    }

    public function testStoreReturns500OnFailure(): void
    {
        $cityMock = $this->createMock(City::class);
        $cityMock->method('register')->willReturn(false);

        $ctrl   = $this->makeController(['name' => 'Campinas'], $cityMock);
        $result = $this->capture(fn() => $ctrl->store());

        $this->assertFalse($result['success']);
        $this->assertSame(500, http_response_code());
    }

    // --- update ---

    public function testUpdateReturnsMissingFieldError(): void
    {
        $cityMock = $this->createMock(City::class);
        $cityMock->method('show')->willReturn([['id' => 1, 'name' => 'SP']]);

        $ctrl   = $this->makeController(['name' => ''], $cityMock);
        $result = $this->capture(fn() => $ctrl->update('1'));

        $this->assertFalse($result['success']);
        $this->assertSame(422, http_response_code());
    }

    public function testUpdateReturns404WhenCityNotFound(): void
    {
        $cityMock = $this->createMock(City::class);
        $cityMock->method('show')->willReturn(false);

        $ctrl   = $this->makeController(['name' => 'Nova SP'], $cityMock);
        $result = $this->capture(fn() => $ctrl->update('99'));

        $this->assertFalse($result['success']);
        $this->assertSame(404, http_response_code());
    }

    public function testUpdateReturns200OnSuccess(): void
    {
        $cityMock = $this->createMock(City::class);
        $cityMock->method('show')->willReturn([['id' => 1, 'name' => 'SP']]);
        $cityMock->method('edit')->willReturn(true);

        $ctrl   = $this->makeController(['name' => 'Nova SP'], $cityMock);
        $result = $this->capture(fn() => $ctrl->update('1'));

        $this->assertTrue($result['success']);
        $this->assertSame(200, http_response_code());
    }

    // --- destroy ---

    public function testDestroyReturns404WhenCityNotFound(): void
    {
        $cityMock = $this->createMock(City::class);
        $cityMock->method('show')->willReturn(false);

        $ctrl   = $this->makeController([], $cityMock);
        $result = $this->capture(fn() => $ctrl->destroy('99'));

        $this->assertFalse($result['success']);
        $this->assertSame(404, http_response_code());
    }

    public function testDestroyReturns200OnSuccess(): void
    {
        $cityMock = $this->createMock(City::class);
        $cityMock->method('show')->willReturn([['id' => 1, 'name' => 'SP']]);
        $cityMock->method('delete')->willReturn(true);

        $ctrl   = $this->makeController([], $cityMock);
        $result = $this->capture(fn() => $ctrl->destroy('1'));

        $this->assertTrue($result['success']);
        $this->assertSame(200, http_response_code());
    }
}
