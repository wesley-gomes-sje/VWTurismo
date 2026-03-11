<?php

namespace Tests\Unit\Controller\Api;

use App\Controller\Api\TicketApiController;
use App\Model\Route;
use App\Model\Ticket;

class TicketApiControllerTest extends ApiTestCase
{
    private function makeController(array $body, ?Ticket $ticket = null, ?Route $route = null): TicketApiController
    {
        $ticket = $ticket ?? $this->createMock(Ticket::class);
        $route  = $route  ?? $this->createMock(Route::class);

        return new class($body, $ticket, $route) extends TicketApiController {
            public function __construct(
                private array  $fakeBody,
                ?Ticket        $ticket = null,
                ?Route         $route  = null
            ) {
                parent::__construct($ticket, $route);
            }
            protected function body(): array { return $this->fakeBody; }
        };
    }

    // --- index ---

    public function testIndexReturnsAllTicketsForAdmin(): void
    {
        $this->authenticate('admin', 1);
        $ticketMock = $this->createMock(Ticket::class);
        $ticketMock->method('all')->willReturn([['id' => 1], ['id' => 2]]);

        $ctrl   = $this->makeController([], $ticketMock);
        $result = $this->capture(fn() => $ctrl->index());

        $this->assertTrue($result['success']);
        $this->assertCount(2, $result['data']);
    }

    public function testIndexReturnsOwnTicketsForUser(): void
    {
        $this->authenticate('user', 5);
        $ticketMock = $this->createMock(Ticket::class);
        $ticketMock->method('showTicketsByPassenger')->with(5)->willReturn([['id' => 3]]);

        $ctrl   = $this->makeController([], $ticketMock);
        $result = $this->capture(fn() => $ctrl->index());

        $this->assertTrue($result['success']);
        $this->assertCount(1, $result['data']);
    }

    // --- show ---

    public function testShowReturns404WhenNotFound(): void
    {
        $ticketMock = $this->createMock(Ticket::class);
        $ticketMock->method('show')->willReturn(false);

        $ctrl   = $this->makeController([], $ticketMock);
        $result = $this->capture(fn() => $ctrl->show('999'));

        $this->assertFalse($result['success']);
        $this->assertSame(404, http_response_code());
    }

    public function testShowReturnsTicketData(): void
    {
        $ticketMock = $this->createMock(Ticket::class);
        $ticketMock->method('show')->willReturn(['id' => 1, 'price' => '50.00']);

        $ctrl   = $this->makeController([], $ticketMock);
        $result = $this->capture(fn() => $ctrl->show('1'));

        $this->assertTrue($result['success']);
        $this->assertSame('50.00', $result['data']['price']);
    }

    // --- store ---

    public function testStoreReturnsMissingFieldError(): void
    {
        $this->authenticate('user', 1);
        $ctrl   = $this->makeController(['origin' => '', 'destination' => '2', 'vehicle_id' => 1, 'date' => '2025-01-01']);
        $result = $this->capture(fn() => $ctrl->store());

        $this->assertFalse($result['success']);
        $this->assertSame(422, http_response_code());
    }

    public function testStoreReturns404WhenRouteNotFound(): void
    {
        $this->authenticate('user', 1);
        $routeMock = $this->createMock(Route::class);
        $routeMock->method('check')->willReturn(false);

        $ctrl   = $this->makeController(['origin' => '1', 'destination' => '2', 'vehicle_id' => 1, 'date' => '2025-01-01'], null, $routeMock);
        $result = $this->capture(fn() => $ctrl->store());

        $this->assertFalse($result['success']);
        $this->assertSame(404, http_response_code());
    }

    public function testStoreReturns201OnSuccess(): void
    {
        $this->authenticate('user', 1);
        $routeMock  = $this->createMock(Route::class);
        $routeMock->method('check')->willReturn(['id' => 10, 'distance' => 200.0]);
        $ticketMock = $this->createMock(Ticket::class);
        $ticketMock->method('calculatePrice')->willReturn(100.0);
        $ticketMock->method('register')->willReturn(true);
        $ticketMock->method('getPrice')->willReturn(100.0);

        $ctrl   = $this->makeController(['origin' => '1', 'destination' => '2', 'vehicle_id' => 3, 'date' => '2025-01-01'], $ticketMock, $routeMock);
        $result = $this->capture(fn() => $ctrl->store());

        $this->assertTrue($result['success']);
        $this->assertSame(201, http_response_code());
    }
}
