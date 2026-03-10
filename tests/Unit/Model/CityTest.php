<?php

namespace Tests\Unit\Model;

use City;
use PDO;
use PDOException;
use PDOStatement;
use PHPUnit\Framework\TestCase;

class CityTest extends TestCase
{
    private function makePdo(PDOStatement $stmt): PDO
    {
        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willReturn($stmt);
        return $pdo;
    }

    private function makeStmt(bool $executes = true, int $rows = 0, array $data = []): PDOStatement
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('bindValue')->willReturn(true);
        $stmt->method('execute')->willReturn($executes);
        $stmt->method('rowCount')->willReturn($rows);
        $stmt->method('fetchAll')->willReturn($data);
        return $stmt;
    }

    // --- register() ---

    public function testRegisterReturnsTrueOnSuccess(): void
    {
        $pdo  = $this->makePdo($this->makeStmt(true));
        $city = new City($pdo);
        $city->setName('São Paulo');

        $this->assertTrue($city->register());
    }

    public function testRegisterReturnsFalseWhenExecuteFails(): void
    {
        $pdo  = $this->makePdo($this->makeStmt(false));
        $city = new City($pdo);
        $city->setName('São Paulo');

        $this->assertFalse($city->register());
    }

    public function testRegisterReturnsFalseOnException(): void
    {
        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willThrowException(new PDOException('fail'));
        $city = new City($pdo);

        $this->assertFalse($city->register());
    }

    // --- all() ---

    public function testAllReturnsDataOnSuccess(): void
    {
        $rows = [['id' => 1, 'name' => 'SP'], ['id' => 2, 'name' => 'RJ']];
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('fetchAll')->willReturn($rows);

        $pdo = $this->createMock(PDO::class);
        $pdo->method('query')->willReturn($stmt);

        $city   = new City($pdo);
        $result = $city->all();

        $this->assertSame($rows, $result);
    }

    public function testAllReturnsEmptyArrayOnException(): void
    {
        $pdo = $this->createMock(PDO::class);
        $pdo->method('query')->willThrowException(new PDOException('fail'));

        $city   = new City($pdo);
        $result = $city->all();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    // --- show() ---

    public function testShowReturnsCityWhenFound(): void
    {
        $data = [['id' => 1, 'name' => 'SP']];
        $stmt = $this->makeStmt(true, 1, $data);
        $city = new City($this->makePdo($stmt));

        $this->assertSame($data, $city->show(1));
    }

    public function testShowReturnsFalseWhenNotFound(): void
    {
        $stmt = $this->makeStmt(true, 0, []);
        $city = new City($this->makePdo($stmt));

        $this->assertFalse($city->show(99));
    }

    // --- delete() ---

    public function testDeleteReturnsTrueOnSuccess(): void
    {
        $city = new City($this->makePdo($this->makeStmt(true)));
        $this->assertTrue($city->delete(1));
    }

    public function testDeleteReturnsFalseOnException(): void
    {
        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willThrowException(new PDOException('fail'));
        $city = new City($pdo);

        $this->assertFalse($city->delete(1));
    }

    // --- edit() ---

    public function testEditReturnsTrueOnSuccess(): void
    {
        $city = new City($this->makePdo($this->makeStmt(true)));
        $this->assertTrue($city->edit(1, 'Novo Nome'));
    }

    public function testEditReturnsFalseOnException(): void
    {
        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willThrowException(new PDOException('fail'));
        $city = new City($pdo);

        $this->assertFalse($city->edit(1, 'Novo Nome'));
    }

    // --- getters / setters ---

    public function testSetAndGetId(): void
    {
        $city = new City($this->createMock(PDO::class));
        $city->setId(5);
        $this->assertSame(5, $city->getId());
    }

    public function testSetAndGetName(): void
    {
        $city = new City($this->createMock(PDO::class));
        $city->setName('Campinas');
        $this->assertSame('Campinas', $city->getName());
    }
}
