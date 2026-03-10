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
    // --- login() ---

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

        $login = new Login($pdo);

        $this->assertFalse($login->login('naoexiste@example.com', 'senha123'));
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testLoginReturnsFalseWhenPasswordIsWrong(): void
    {
        $hashedCorrect = password_hash('senhaCorreta', PASSWORD_BCRYPT);

        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('bindValue')->willReturn(true);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('rowCount')->willReturn(1);
        $stmt->method('fetchAll')->willReturn([
            ['id' => 1, 'email' => 'user@example.com', 'password' => $hashedCorrect,
             'profile' => 'user', 'name' => 'Alice'],
        ]);

        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willReturn($stmt);

        $login = new Login($pdo);

        $this->assertFalse($login->login('user@example.com', 'senhaErrada'));
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testLoginReturnsTrueWithValidCredentials(): void
    {
        $password       = 'senha_correta';
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('bindValue')->willReturn(true);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('rowCount')->willReturn(1);
        $stmt->method('fetchAll')->willReturn([
            ['id' => 1, 'email' => 'user@example.com', 'password' => $hashedPassword,
             'profile' => 'user', 'name' => 'Alice'],
        ]);

        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willReturn($stmt);

        $login  = new Login($pdo);
        $result = $login->login('user@example.com', $password);

        $this->assertTrue($result);
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testLoginSetsSessionOnSuccess(): void
    {
        $password       = 'senha_correta';
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('bindValue')->willReturn(true);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('rowCount')->willReturn(1);
        $stmt->method('fetchAll')->willReturn([
            ['id' => 42, 'email' => 'user@example.com', 'password' => $hashedPassword,
             'profile' => 'admin', 'name' => 'Alice'],
        ]);

        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willReturn($stmt);

        $login = new Login($pdo);
        $login->login('user@example.com', $password);

        $this->assertSame(42,      $_SESSION['idUser']);
        $this->assertSame('admin', $_SESSION['profile']);
        $this->assertSame('Alice', $_SESSION['name']);
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testLoginReturnsFalseOnPdoException(): void
    {
        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willThrowException(new PDOException('fail'));

        $login = new Login($pdo);

        $this->assertFalse($login->login('user@example.com', 'senha'));
    }

    // --- getters / setters ---

    public function testSetAndGetEmail(): void
    {
        $pdo   = $this->createMock(PDO::class);
        $login = new Login($pdo);
        $login->setEmail('test@example.com');

        $this->assertSame('test@example.com', $login->getEmail());
    }

    public function testSetAndGetPassword(): void
    {
        $pdo   = $this->createMock(PDO::class);
        $login = new Login($pdo);
        $login->setPassword('secret');

        $this->assertSame('secret', $login->getPassword());
    }
}
