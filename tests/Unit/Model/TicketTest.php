<?php

namespace Tests\Unit\Model;

use PDO;
use PDOException;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use App\Model\Ticket;

class TicketTest extends TestCase
{
    private function makeSuccessStatement(): PDOStatement
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('bindValue')->willReturn(true);
        $stmt->method('execute')->willReturn(true);
        return $stmt;
    }

    private function makeFailStatement(): PDOStatement
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('bindValue')->willReturn(true);
        $stmt->method('execute')->willReturn(false);
        $stmt->method('errorInfo')->willReturn(['HY000', 1, 'DB error']);
        return $stmt;
    }

    // --- register() ---

    public function testRegisterReturnsTrueOnSuccess(): void
    {
        $stmt = $this->makeSuccessStatement();

        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willReturn($stmt);

        $ticket = new Ticket($pdo);
        $ticket->setPassenger(1);
        $ticket->setRoute(2);
        $ticket->setVehicle(3);
        $ticket->setPrice(150.0);
        $ticket->setDate('2025-06-01');

        $this->assertTrue($ticket->register());
    }

    public function testRegisterReturnsFalseWhenExecuteFails(): void
    {
        $stmt = $this->makeFailStatement();

        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willReturn($stmt);

        $ticket = new Ticket($pdo);
        $ticket->setPassenger(1);
        $ticket->setRoute(2);
        $ticket->setVehicle(3);
        $ticket->setPrice(150.0);
        $ticket->setDate('2025-06-01');

        $this->assertFalse($ticket->register());
    }

    public function testRegisterReturnsFalseOnPdoException(): void
    {
        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willThrowException(new PDOException('fail'));

        $ticket = new Ticket($pdo);

        $this->assertFalse($ticket->register());
    }

    // --- all() ---

    public function testAllReturnsArrayOfTickets(): void
    {
        $expected = [
            ['name' => 'Alice', 'date' => '2025-06-01', 'price' => '150.00',
             'origin' => 'SP', 'destination' => 'RJ', 'distance' => 400,
             'brand' => 'Mercedes', 'model' => 'Torino', 'plate' => 'ABC1234'],
        ];

        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('fetchAll')->willReturn($expected);

        $pdo = $this->createMock(PDO::class);
        $pdo->method('query')->willReturn($stmt);

        $ticket = new Ticket($pdo);

        $this->assertSame($expected, $ticket->all());
    }

    public function testAllReturnsEmptyArrayOnPdoException(): void
    {
        $pdo = $this->createMock(PDO::class);
        $pdo->method('query')->willThrowException(new PDOException('fail'));

        $ticket = new Ticket($pdo);

        $this->assertSame([], $ticket->all());
    }

    // --- showTicketsByPassenger() ---

    public function testShowTicketsByPassengerReturnsArray(): void
    {
        $expected = [
            ['date' => '2025-06-01', 'price' => '150.00', 'origin' => 'SP', 'destination' => 'RJ'],
        ];

        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('bindValue')->willReturn(true);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('fetchAll')->willReturn($expected);

        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willReturn($stmt);

        $ticket = new Ticket($pdo);
        $result = $ticket->showTicketsByPassenger(1);

        $this->assertSame($expected, $result);
    }

    public function testShowTicketsByPassengerReturnsFalseOnException(): void
    {
        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willThrowException(new PDOException('fail'));

        $ticket = new Ticket($pdo);

        $this->assertFalse($ticket->showTicketsByPassenger(1));
    }

    // --- calculatePrice() ---

    public function testCalculatePriceReturnsDistanceTimesHalf(): void
    {
        $pdo    = $this->createMock(PDO::class);
        $ticket = new Ticket($pdo);

        $this->assertSame(150.0, $ticket->calculatePrice(300));
    }

    public function testCalculatePriceForZeroDistanceIsZero(): void
    {
        $pdo    = $this->createMock(PDO::class);
        $ticket = new Ticket($pdo);

        $this->assertSame(0.0, $ticket->calculatePrice(0));
    }

    public function testCalculatePriceWithDecimalDistance(): void
    {
        $pdo    = $this->createMock(PDO::class);
        $ticket = new Ticket($pdo);

        $this->assertSame(75.25, $ticket->calculatePrice(150.5));
    }

    // --- getters / setters ---

    public function testSetAndGetPrice(): void
    {
        $pdo    = $this->createMock(PDO::class);
        $ticket = new Ticket($pdo);
        $ticket->setPrice(200.0);

        $this->assertSame(200.0, $ticket->getPrice());
    }

    public function testSetAndGetDate(): void
    {
        $pdo    = $this->createMock(PDO::class);
        $ticket = new Ticket($pdo);
        $ticket->setDate('2025-12-25');

        $this->assertSame('2025-12-25', $ticket->getDate());
    }
}
