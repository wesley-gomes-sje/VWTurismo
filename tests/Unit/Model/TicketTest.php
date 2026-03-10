<?php

namespace Tests\Unit\Model;

use Ticket;
use PDO;
use PDOException;
use PDOStatement;
use PHPUnit\Framework\TestCase;

class TicketTest extends TestCase
{
    private function makeStmt(bool $executes = true, mixed $fetchReturn = false, array $fetchAll = []): PDOStatement
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('bindValue')->willReturn(true);
        $stmt->method('execute')->willReturn($executes);
        $stmt->method('fetch')->willReturn($fetchReturn);
        $stmt->method('fetchAll')->willReturn($fetchAll);
        return $stmt;
    }

    private function makePdo(PDOStatement $stmt): PDO
    {
        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willReturn($stmt);
        return $pdo;
    }

    // --- register() ---

    public function testRegisterReturnsTrueOnSuccess(): void
    {
        $t = new Ticket($this->makePdo($this->makeStmt(true)));
        $t->setPassenger(1);
        $t->setRoute(1);
        $t->setVehicle(1);
        $t->setPrice(150.0);
        $t->setDate('2025-01-01');

        $this->assertTrue($t->register());
    }

    public function testRegisterReturnsFalseWhenExecuteFails(): void
    {
        $this->assertFalse((new Ticket($this->makePdo($this->makeStmt(false))))->register());
    }

    public function testRegisterReturnsFalseOnException(): void
    {
        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willThrowException(new PDOException('fail'));
        $this->assertFalse((new Ticket($pdo))->register());
    }

    // --- show() ---

    public function testShowReturnsTicketWhenFound(): void
    {
        $row  = ['date' => '2025-01-01', 'price' => '150.00', 'origin' => 'SP', 'destination' => 'RJ', 'distance' => 300];
        $stmt = $this->makeStmt(true, $row);

        $this->assertSame($row, (new Ticket($this->makePdo($stmt)))->show(1));
    }

    public function testShowReturnsFalseOnException(): void
    {
        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willThrowException(new PDOException('fail'));
        $this->assertFalse((new Ticket($pdo))->show(1));
    }

    // --- all() ---

    public function testAllReturnsData(): void
    {
        $rows = [['name' => 'Alice', 'date' => '2025-01-01', 'price' => '150.00']];
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('fetchAll')->willReturn($rows);

        $pdo = $this->createMock(PDO::class);
        $pdo->method('query')->willReturn($stmt);

        $this->assertSame($rows, (new Ticket($pdo))->all());
    }

    public function testAllReturnsEmptyArrayOnException(): void
    {
        $pdo = $this->createMock(PDO::class);
        $pdo->method('query')->willThrowException(new PDOException('fail'));

        $result = (new Ticket($pdo))->all();
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    // --- showTicketsByPassenger() ---

    public function testShowTicketsByPassengerReturnsData(): void
    {
        $rows = [['date' => '2025-01-01', 'origin' => 'SP', 'destination' => 'RJ']];
        $stmt = $this->makeStmt(true, false, $rows);

        $this->assertSame($rows, (new Ticket($this->makePdo($stmt)))->showTicketsByPassenger(1));
    }

    public function testShowTicketsByPassengerReturnsFalseOnException(): void
    {
        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willThrowException(new PDOException('fail'));
        $this->assertFalse((new Ticket($pdo))->showTicketsByPassenger(1));
    }

    // --- calculatePrice() ---

    public function testCalculatePriceReturnsCorrectValue(): void
    {
        $t = new Ticket($this->createMock(PDO::class));
        $this->assertSame(150.0,  $t->calculatePrice(300));
        $this->assertSame(0.0,    $t->calculatePrice(0));
        $this->assertSame(75.25,  $t->calculatePrice(150.5));
    }

    // --- getters / setters ---

    public function testGettersAndSetters(): void
    {
        $t = new Ticket($this->createMock(PDO::class));
        $t->setId(1);
        $t->setPassenger(2);
        $t->setRoute(3);
        $t->setVehicle(4);
        $t->setPrice(99.99);
        $t->setDate('2025-06-01');

        $this->assertSame(1,          $t->getId());
        $this->assertSame(2,          $t->getPassenger());
        $this->assertSame(3,          $t->getRoute());
        $this->assertSame(4,          $t->getVehicle());
        $this->assertSame(99.99,      $t->getPrice());
        $this->assertSame('2025-06-01', $t->getDate());
    }
}
