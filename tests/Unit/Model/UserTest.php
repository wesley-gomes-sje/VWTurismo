<?php

namespace Tests\Unit\Model;

use App\Model\User;
use PDO;
use PDOException;
use PDOStatement;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private function makeStmt(bool $executes = true, int $rows = 0, array $data = [], mixed $fetchReturn = false): PDOStatement
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('bindValue')->willReturn(true);
        $stmt->method('execute')->willReturn($executes);
        $stmt->method('rowCount')->willReturn($rows);
        $stmt->method('fetchAll')->willReturn($data);
        $stmt->method('fetch')->willReturn($fetchReturn);
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
        $u = new User(null, $this->makePdo($this->makeStmt(true)));
        $u->setName('Alice');
        $u->setEmail('alice@example.com');
        $u->setPassword('hash');

        $this->assertTrue($u->register());
    }

    public function testRegisterReturnsFalseWhenExecuteFails(): void
    {
        $u = new User(null, $this->makePdo($this->makeStmt(false)));
        $this->assertFalse($u->register());
    }

    public function testRegisterReturnsFalseOnException(): void
    {
        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willThrowException(new PDOException('fail'));
        $this->assertFalse((new User(null, $pdo))->register());
    }

    // --- show() ---

    public function testShowReturnsTrueWhenUserExists(): void
    {
        $u = new User(null, $this->makePdo($this->makeStmt(true, 1)));
        $this->assertTrue($u->show('alice@example.com'));
    }

    public function testShowReturnsFalseWhenNotFound(): void
    {
        $u = new User(null, $this->makePdo($this->makeStmt(true, 0)));
        $this->assertFalse($u->show('nope@example.com'));
    }

    // --- all() ---

    public function testAllReturnsData(): void
    {
        $rows = [['name' => 'Alice', 'email' => 'alice@example.com']];
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('fetchAll')->willReturn($rows);

        $pdo = $this->createMock(PDO::class);
        $pdo->method('query')->willReturn($stmt);

        $this->assertSame($rows, (new User(null, $pdo))->all());
    }

    public function testAllReturnsEmptyArrayOnException(): void
    {
        $pdo = $this->createMock(PDO::class);
        $pdo->method('query')->willThrowException(new PDOException('fail'));

        $result = (new User(null, $pdo))->all();
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    // --- getters / setters ---

    public function testGettersAndSetters(): void
    {
        $u = new User(null, $this->createMock(PDO::class));
        $u->setId(1);
        $u->setName('Bob');
        $u->setEmail('bob@example.com');
        $u->setPassword('secret');

        $this->assertSame(1,                 $u->getId());
        $this->assertSame('Bob',             $u->getName());
        $this->assertSame('bob@example.com', $u->getEmail());
        $this->assertSame('secret',          $u->getPassword());
    }
}
