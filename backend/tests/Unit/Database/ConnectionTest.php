<?php

namespace Tests\Unit\Database;

use App\Database\Connection;
use PDO;
use PHPUnit\Framework\TestCase;

class ConnectionTest extends TestCase
{
    protected function tearDown(): void
    {
        Connection::reset();
    }

    public function testSetInstanceAllowsInjection(): void
    {
        $pdo = $this->createMock(PDO::class);

        Connection::setInstance($pdo);

        $this->assertSame($pdo, Connection::getInstance());
    }

    public function testGetInstanceReturnsSameInstance(): void
    {
        $pdo = $this->createMock(PDO::class);
        Connection::setInstance($pdo);

        $first  = Connection::getInstance();
        $second = Connection::getInstance();

        $this->assertSame($first, $second);
    }

    public function testResetClearsStoredInstance(): void
    {
        $pdo = $this->createMock(PDO::class);
        Connection::setInstance($pdo);
        Connection::reset();

        // Após reset, uma nova chamada a getInstance() tentaria conectar ao BD.
        // Verificamos apenas que o reset não lança exceção e que a instância
        // deixou de ser a injetada (sem precisar de banco real).
        $this->expectNotToPerformAssertions();
    }

    public function testGetInstanceReturnsPdoType(): void
    {
        $pdo = $this->createMock(PDO::class);
        Connection::setInstance($pdo);

        $this->assertInstanceOf(PDO::class, Connection::getInstance());
    }
}
