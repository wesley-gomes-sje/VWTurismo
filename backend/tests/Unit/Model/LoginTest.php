<?php

namespace Tests\Unit\Model;

use App\Model\Login;
use PDO;
use PDOException;
use PDOStatement;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase
{
    // --- login() retorna false ---

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testLoginReturnsFalseWhenUserNotFound(): void
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('bindValue')->willReturn(true);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('rowCount')->willReturn(0);

        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willReturn($stmt);

        $this->assertFalse((new Login($pdo))->login('x@x.com', 'wrong'));
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testLoginReturnsFalseWhenPasswordIsWrong(): void
    {
        $hashed = password_hash('correta', PASSWORD_BCRYPT);

        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('bindValue')->willReturn(true);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('rowCount')->willReturn(1);
        $stmt->method('fetchAll')->willReturn([
            ['id' => 1, 'email' => 'u@u.com', 'password' => $hashed, 'profile' => 'user', 'name' => 'Alice'],
        ]);

        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willReturn($stmt);

        $this->assertFalse((new Login($pdo))->login('u@u.com', 'errada'));
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testLoginReturnsFalseOnPdoException(): void
    {
        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willThrowException(new PDOException('fail'));

        $this->assertFalse((new Login($pdo))->login('u@u.com', 'senha'));
    }

    // --- login() retorna array com dados do usuário ---

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testLoginReturnsUserArrayOnSuccess(): void
    {
        $password = 'senha_correta';
        $hashed   = password_hash($password, PASSWORD_BCRYPT);
        $userData = ['id' => 42, 'email' => 'u@u.com', 'password' => $hashed, 'profile' => 'admin', 'name' => 'Alice'];

        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('bindValue')->willReturn(true);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('rowCount')->willReturn(1);
        $stmt->method('fetchAll')->willReturn([$userData]);

        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willReturn($stmt);

        $result = (new Login($pdo))->login('u@u.com', $password);

        $this->assertIsArray($result);
        $this->assertSame(42,      $result['id']);
        $this->assertSame('admin', $result['profile']);
        $this->assertSame('Alice', $result['name']);
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testLoginDoesNotSetSessionDirectly(): void
    {
        $password = 'senha_correta';
        $hashed   = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('bindValue')->willReturn(true);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('rowCount')->willReturn(1);
        $stmt->method('fetchAll')->willReturn([
            ['id' => 7, 'email' => 'u@u.com', 'password' => $hashed, 'profile' => 'user', 'name' => 'Bob'],
        ]);

        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willReturn($stmt);

        (new Login($pdo))->login('u@u.com', $password);

        // Model não deve mais tocar em $_SESSION
        $this->assertArrayNotHasKey('idUser', $_SESSION ?? []);
    }

    // --- getters / setters ---

    public function testSetAndGetEmail(): void
    {
        $login = new Login($this->createMock(PDO::class));
        $login->setEmail('test@example.com');
        $this->assertSame('test@example.com', $login->getEmail());
    }

    public function testSetAndGetPassword(): void
    {
        $login = new Login($this->createMock(PDO::class));
        $login->setPassword('secret');
        $this->assertSame('secret', $login->getPassword());
    }
}
