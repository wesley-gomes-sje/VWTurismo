<?php

namespace Tests\Unit\Model;

use PDO;
use PDOException;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use App\Model\Route;

class RouteTest extends TestCase
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

        $route = new Route($pdo);
        $route->setOrigin(1);
        $route->setDestination(2);
        $route->setDistance(300);

        $this->assertTrue($route->register());
    }

    public function testRegisterReturnsFalseWhenExecuteFails(): void
    {
        $stmt = $this->makeFailStatement();

        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willReturn($stmt);

        $route = new Route($pdo);
        $route->setOrigin(1);
        $route->setDestination(2);
        $route->setDistance(300);

        $this->assertFalse($route->register());
    }

    public function testRegisterReturnsFalseOnPdoException(): void
    {
        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willThrowException(new PDOException('fail'));

        $route = new Route($pdo);
        $route->setOrigin(1);
        $route->setDestination(2);
        $route->setDistance(300);

        $this->assertFalse($route->register());
    }

    // --- all() ---

    public function testAllReturnsArrayOfRoutes(): void
    {
        $expected = [
            ['origin' => 'São Paulo', 'destination' => 'Campinas', 'distance' => 100],
        ];

        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('fetchAll')->willReturn($expected);

        $pdo = $this->createMock(PDO::class);
        $pdo->method('query')->willReturn($stmt);

        $route = new Route($pdo);

        $this->assertSame($expected, $route->all());
    }

    public function testAllReturnsEmptyArrayOnPdoException(): void
    {
        $pdo = $this->createMock(PDO::class);
        $pdo->method('query')->willThrowException(new PDOException('fail'));

        $route = new Route($pdo);

        $this->assertSame([], $route->all());
    }

    // --- check() ---

    public function testCheckReturnsRouteDataWhenExists(): void
    {
        $expected = ['id' => 5, 'distance' => 300];

        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('bindValue')->willReturn(true);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('fetch')->willReturn($expected);

        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willReturn($stmt);

        $route  = new Route($pdo);
        $result = $route->check(1, 2);

        $this->assertSame($expected, $result);
    }

    public function testCheckReturnsFalseWhenRouteDoesNotExist(): void
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('bindValue')->willReturn(true);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('fetch')->willReturn(false);

        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willReturn($stmt);

        $route  = new Route($pdo);
        $result = $route->check(1, 99);

        $this->assertFalse($result);
    }

    public function testCheckReturnsFalseOnPdoException(): void
    {
        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willThrowException(new PDOException('fail'));

        $route = new Route($pdo);

        $this->assertFalse($route->check(1, 2));
    }

    // --- getters / setters ---

    public function testSetAndGetDistance(): void
    {
        $pdo   = $this->createMock(PDO::class);
        $route = new Route($pdo);
        $route->setDistance(500);

        $this->assertSame(500, $route->getDistance());
    }
}
