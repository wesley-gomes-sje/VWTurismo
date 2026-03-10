<?php

namespace Tests\Unit\Model;

use Route;
use PDO;
use PDOException;
use PDOStatement;
use PHPUnit\Framework\TestCase;

class RouteTest extends TestCase
{
    private function makeStmt(bool $executes = true, mixed $fetchReturn = false): PDOStatement
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('bindValue')->willReturn(true);
        $stmt->method('execute')->willReturn($executes);
        $stmt->method('fetch')->willReturn($fetchReturn);
        $stmt->method('fetchAll')->willReturn([]);
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
        $route = new Route($this->makePdo($this->makeStmt(true)));
        $route->setOrigin(1);
        $route->setDestination(2);
        $route->setDistance(300);

        $this->assertTrue($route->register());
    }

    public function testRegisterReturnsFalseWhenExecuteFails(): void
    {
        $route = new Route($this->makePdo($this->makeStmt(false)));
        $this->assertFalse($route->register());
    }

    public function testRegisterReturnsFalseOnException(): void
    {
        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willThrowException(new PDOException('fail'));
        $this->assertFalse((new Route($pdo))->register());
    }

    // --- all() ---

    public function testAllReturnsData(): void
    {
        $rows = [['origin' => 'SP', 'destination' => 'RJ', 'distance' => 400]];
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('fetchAll')->willReturn($rows);

        $pdo = $this->createMock(PDO::class);
        $pdo->method('query')->willReturn($stmt);

        $this->assertSame($rows, (new Route($pdo))->all());
    }

    public function testAllReturnsFalseOnException(): void
    {
        $pdo = $this->createMock(PDO::class);
        $pdo->method('query')->willThrowException(new PDOException('fail'));
        $this->assertFalse((new Route($pdo))->all());
    }

    // --- check() ---

    public function testCheckReturnsRouteWhenFound(): void
    {
        $row  = ['id' => 1, 'distance' => 300];
        $stmt = $this->makeStmt(true, $row);
        $route = new Route($this->makePdo($stmt));

        $this->assertSame($row, $route->check(1, 2));
    }

    public function testCheckReturnsFalseWhenNotFound(): void
    {
        $stmt  = $this->makeStmt(true, false);
        $route = new Route($this->makePdo($stmt));

        $this->assertFalse($route->check(1, 99));
    }

    public function testCheckReturnsFalseOnException(): void
    {
        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willThrowException(new PDOException('fail'));
        $this->assertFalse((new Route($pdo))->check(1, 2));
    }

    // --- getters / setters ---

    public function testGettersAndSetters(): void
    {
        $route = new Route($this->createMock(PDO::class));
        $route->setId(1);
        $route->setOrigin(2);
        $route->setDestination(3);
        $route->setDistance(500);

        $this->assertSame(1,   $route->getId());
        $this->assertSame(2,   $route->getOrigin());
        $this->assertSame(3,   $route->getDestination());
        $this->assertSame(500, $route->getDistance());
    }
}
