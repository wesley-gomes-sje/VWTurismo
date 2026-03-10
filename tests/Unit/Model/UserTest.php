<?php

namespace Tests\Unit\Model;

use PDO;
use PDOException;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use User;

class UserTest extends TestCase
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

        $user = new User(null, $pdo);
        $user->setName('João Silva');
        $user->setEmail('joao@example.com');
        $user->setPassword('hashed_password');

        $this->assertTrue($user->register());
    }

    public function testRegisterReturnsFalseWhenExecuteFails(): void
    {
        $stmt = $this->makeFailStatement();

        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willReturn($stmt);

        $user = new User(null, $pdo);
        $user->setName('João');
        $user->setEmail('joao@example.com');
        $user->setPassword('hash');

        $this->assertFalse($user->register());
    }

    public function testRegisterReturnsFalseOnPdoException(): void
    {
        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willThrowException(new PDOException('fail'));

        $user = new User(null, $pdo);
        $user->setName('João');
        $user->setEmail('joao@example.com');
        $user->setPassword('hash');

        $this->assertFalse($user->register());
    }

    // --- all() ---

    public function testAllReturnsArrayOfUsers(): void
    {
        $expected = [
            ['name' => 'Alice', 'email' => 'alice@example.com'],
            ['name' => 'Bob',   'email' => 'bob@example.com'],
        ];

        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('fetchAll')->willReturn($expected);

        $pdo = $this->createMock(PDO::class);
        $pdo->method('query')->willReturn($stmt);

        $user = new User(null, $pdo);

        $this->assertSame($expected, $user->all());
    }

    public function testAllReturnsEmptyArrayOnPdoException(): void
    {
        $pdo = $this->createMock(PDO::class);
        $pdo->method('query')->willThrowException(new PDOException('fail'));

        $user = new User(null, $pdo);

        $this->assertSame([], $user->all());
    }

    // --- show() ---

    public function testShowReturnsTrueWhenEmailExists(): void
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('bindValue')->willReturn(true);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('rowCount')->willReturn(1);

        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willReturn($stmt);

        $user = new User(null, $pdo);

        $this->assertTrue($user->show('alice@example.com'));
    }

    public function testShowReturnsFalseWhenEmailNotFound(): void
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('bindValue')->willReturn(true);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('rowCount')->willReturn(0);

        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willReturn($stmt);

        $user = new User(null, $pdo);

        $this->assertFalse($user->show('naoexiste@example.com'));
    }

    // --- showCustomers() ---

    public function testShowCustomersReturnsOnlyUsers(): void
    {
        $expected = [['name' => 'Cliente', 'email' => 'cliente@example.com']];

        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('fetchAll')->willReturn($expected);

        $pdo = $this->createMock(PDO::class);
        $pdo->method('query')->willReturn($stmt);

        $user = new User(null, $pdo);

        $this->assertSame($expected, $user->showCustomers());
    }

    // --- getters / setters ---

    public function testSetAndGetName(): void
    {
        $pdo  = $this->createMock(PDO::class);
        $user = new User(null, $pdo);
        $user->setName('Maria');

        $this->assertSame('Maria', $user->getName());
    }

    public function testSetAndGetEmail(): void
    {
        $pdo  = $this->createMock(PDO::class);
        $user = new User(null, $pdo);
        $user->setEmail('maria@example.com');

        $this->assertSame('maria@example.com', $user->getEmail());
    }
}
