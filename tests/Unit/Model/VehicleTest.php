<?php

namespace Tests\Unit\Model;

use Vehicle;
use PDO;
use PDOException;
use PDOStatement;
use PHPUnit\Framework\TestCase;

class VehicleTest extends TestCase
{
    private function makeStmt(bool $executes = true): PDOStatement
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('bindValue')->willReturn(true);
        $stmt->method('execute')->willReturn($executes);
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
        $v = new Vehicle($this->makePdo($this->makeStmt(true)));
        $v->setBrand('VW');
        $v->setModel('Bus');
        $v->setPlate('ABC-1234');
        $v->setYear(2022);

        $this->assertTrue($v->register());
    }

    public function testRegisterReturnsFalseWhenExecuteFails(): void
    {
        $v = new Vehicle($this->makePdo($this->makeStmt(false)));
        $this->assertFalse($v->register());
    }

    public function testRegisterReturnsFalseOnException(): void
    {
        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willThrowException(new PDOException('fail'));
        $this->assertFalse((new Vehicle($pdo))->register());
    }

    // --- all() ---

    public function testAllReturnsData(): void
    {
        $rows = [['id' => 1, 'brand' => 'VW', 'model' => 'Bus', 'plate' => 'ABC-1234', 'year' => 2022]];
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('fetchAll')->willReturn($rows);

        $pdo = $this->createMock(PDO::class);
        $pdo->method('query')->willReturn($stmt);

        $this->assertSame($rows, (new Vehicle($pdo))->all());
    }

    public function testAllReturnsEmptyArrayOnException(): void
    {
        $pdo = $this->createMock(PDO::class);
        $pdo->method('query')->willThrowException(new PDOException('fail'));

        $result = (new Vehicle($pdo))->all();
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    // --- getters / setters ---

    public function testGettersAndSetters(): void
    {
        $v = new Vehicle($this->createMock(PDO::class));
        $v->setId(1);
        $v->setBrand('VW');
        $v->setModel('Bus');
        $v->setPlate('XYZ-9999');
        $v->setYear(2020);

        $this->assertSame(1,          $v->getId());
        $this->assertSame('VW',       $v->getBrand());
        $this->assertSame('Bus',      $v->getModel());
        $this->assertSame('XYZ-9999', $v->getPlate());
        $this->assertSame(2020,       $v->getYear());
    }
}
