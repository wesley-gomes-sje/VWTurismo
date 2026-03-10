<?php

namespace Tests\Unit\Model;

use City;
use PDO;
use PDOException;
use PDOStatement;
use PHPUnit\Framework\TestCase;

class CityTest extends TestCase
{
    private function makePdoWithStatement(PDOStatement $stmt): PDO
    {
        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willReturn($stmt);
        return $pdo;
    }

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
        $pdo  = $this->makePdoWithStatement($stmt);

        $city = new City($pdo);
        $city->setName('São Paulo');

        $this->assertTrue($city->register());
    }

    public function testRegisterReturnsFalseWhenExecuteFails(): void
    {
        $stmt = $this->makeFailStatement();
        $pdo  = $this->makePdoWithStatement($stmt);

        $city = new City($pdo);
        $city->setName('São Paulo');

        $this->assertFalse($city->register());
    }

    public function testRegisterReturnsFalseOnPdoException(): void
    {
        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willThrowException(new PDOException('Connection failed'));

        $city = new City($pdo);
        $city->setName('São Paulo');

        $this->assertFalse($city->register());
    }

    // --- all() ---

    public function testAllReturnsArrayOfCities(): void
    {
        $expected = [
            ['id' => 1, 'name' => 'São Paulo', 'status' => 1],
            ['id' => 2, 'name' => 'Rio de Janeiro', 'status' => 1],
        ];

        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('fetchAll')->willReturn($expected);

        $pdo = $this->createMock(PDO::class);
        $pdo->method('query')->willReturn($stmt);

        $city = new City($pdo);

        $this->assertSame($expected, $city->all());
    }

    public function testAllReturnsEmptyArrayOnPdoException(): void
    {
        $pdo = $this->createMock(PDO::class);
        $pdo->method('query')->willThrowException(new PDOException('fail'));

        $city = new City($pdo);

        $this->assertSame([], $city->all());
    }

    // --- show() ---

    public function testShowReturnsCityDataWhenFound(): void
    {
        $expected = [['id' => 1, 'name' => 'São Paulo']];

        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('bindValue')->willReturn(true);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('rowCount')->willReturn(1);
        $stmt->method('fetchAll')->willReturn($expected);

        $pdo = $this->makePdoWithStatement($stmt);

        $city   = new City($pdo);
        $result = $city->show(1);

        $this->assertSame($expected, $result);
    }

    public function testShowReturnsEmptyArrayWhenNotFound(): void
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('bindValue')->willReturn(true);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('rowCount')->willReturn(0);

        $pdo = $this->makePdoWithStatement($stmt);

        $city   = new City($pdo);
        $result = $city->show(999);

        $this->assertSame([], $result);
    }

    // --- delete() ---

    public function testDeleteReturnsTrueOnSuccess(): void
    {
        $stmt = $this->makeSuccessStatement();
        $pdo  = $this->makePdoWithStatement($stmt);

        $city = new City($pdo);

        $this->assertTrue($city->delete(1));
    }

    public function testDeleteReturnsFalseWhenExecuteFails(): void
    {
        $stmt = $this->makeFailStatement();
        $pdo  = $this->makePdoWithStatement($stmt);

        $city = new City($pdo);

        $this->assertFalse($city->delete(1));
    }

    public function testDeleteReturnsFalseOnPdoException(): void
    {
        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willThrowException(new PDOException('fail'));

        $city = new City($pdo);

        $this->assertFalse($city->delete(1));
    }

    // --- edit() ---

    public function testEditReturnsTrueOnSuccess(): void
    {
        $stmt = $this->makeSuccessStatement();
        $pdo  = $this->makePdoWithStatement($stmt);

        $city = new City($pdo);

        $this->assertTrue($city->edit(1, 'Campinas'));
    }

    public function testEditReturnsFalseWhenExecuteFails(): void
    {
        $stmt = $this->makeFailStatement();
        $pdo  = $this->makePdoWithStatement($stmt);

        $city = new City($pdo);

        $this->assertFalse($city->edit(1, 'Campinas'));
    }

    public function testEditReturnsFalseOnPdoException(): void
    {
        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willThrowException(new PDOException('fail'));

        $city = new City($pdo);

        $this->assertFalse($city->edit(1, 'Campinas'));
    }

    // --- getters / setters ---

    public function testSetAndGetName(): void
    {
        $pdo  = $this->createMock(PDO::class);
        $city = new City($pdo);
        $city->setName('Curitiba');

        $this->assertSame('Curitiba', $city->getName());
    }

    public function testSetAndGetId(): void
    {
        $pdo  = $this->createMock(PDO::class);
        $city = new City($pdo);
        $city->setId(42);

        $this->assertSame(42, $city->getId());
    }
}
