<?php

namespace Tests\Unit\Model;

use PDO;
use PDOException;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use Vehicle;

class VehicleTest extends TestCase
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

        $vehicle = new Vehicle($pdo);
        $vehicle->setBrand('Mercedes');
        $vehicle->setModel('Torino');
        $vehicle->setPlate('ABC1234');
        $vehicle->setYear('2020');

        $this->assertTrue($vehicle->register());
    }

    public function testRegisterReturnsFalseWhenExecuteFails(): void
    {
        $stmt = $this->makeFailStatement();

        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willReturn($stmt);

        $vehicle = new Vehicle($pdo);
        $vehicle->setBrand('Mercedes');
        $vehicle->setModel('Torino');
        $vehicle->setPlate('ABC1234');
        $vehicle->setYear('2020');

        $this->assertFalse($vehicle->register());
    }

    public function testRegisterReturnsFalseOnPdoException(): void
    {
        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willThrowException(new PDOException('fail'));

        $vehicle = new Vehicle($pdo);
        $vehicle->setBrand('Mercedes');
        $vehicle->setModel('Torino');
        $vehicle->setPlate('ABC1234');
        $vehicle->setYear('2020');

        $this->assertFalse($vehicle->register());
    }

    // --- all() ---

    public function testAllReturnsArrayOfVehicles(): void
    {
        $expected = [
            ['id' => 1, 'brand' => 'Mercedes', 'model' => 'Torino', 'plate' => 'ABC1234', 'year' => '2020'],
        ];

        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('fetchAll')->willReturn($expected);

        $pdo = $this->createMock(PDO::class);
        $pdo->method('query')->willReturn($stmt);

        $vehicle = new Vehicle($pdo);

        $this->assertSame($expected, $vehicle->all());
    }

    public function testAllReturnsEmptyArrayOnPdoException(): void
    {
        $pdo = $this->createMock(PDO::class);
        $pdo->method('query')->willThrowException(new PDOException('fail'));

        $vehicle = new Vehicle($pdo);

        $this->assertSame([], $vehicle->all());
    }

    // --- getters / setters ---

    public function testSetAndGetBrand(): void
    {
        $pdo     = $this->createMock(PDO::class);
        $vehicle = new Vehicle($pdo);
        $vehicle->setBrand('Volvo');

        $this->assertSame('Volvo', $vehicle->getBrand());
    }

    public function testSetAndGetPlate(): void
    {
        $pdo     = $this->createMock(PDO::class);
        $vehicle = new Vehicle($pdo);
        $vehicle->setPlate('XYZ9999');

        $this->assertSame('XYZ9999', $vehicle->getPlate());
    }
}
