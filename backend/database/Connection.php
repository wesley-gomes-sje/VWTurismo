<?php

namespace App\Database;

use Dotenv\Dotenv;
use PDO;
use PDOException;

class Connection
{
    private static ?PDO $instance = null;

    private function __construct() {}
    private function __clone() {}

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            self::$instance = self::createPdo();
        }

        return self::$instance;
    }

    /**
     * Injeta uma instância de PDO externamente.
     * Útil para testes unitários (injetar um mock).
     */
    public static function setInstance(PDO $pdo): void
    {
        self::$instance = $pdo;
    }

    /**
     * Limpa a instância armazenada.
     * Útil para isolar testes.
     */
    public static function reset(): void
    {
        self::$instance = null;
    }

    private static function createPdo(): PDO
    {
        $dotenv = Dotenv::createImmutable(dirname(__DIR__));
        $dotenv->load();

        $host   = $_ENV['MYSQL_DB_HOST'];
        $dbname = $_ENV['MYSQL_DATABASE'];
        $user   = $_ENV['MYSQL_USER'];
        $pass   = $_ENV['MYSQL_PASSWORD'];

        $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";

        $pdo = new PDO($dsn, $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        return $pdo;
    }
}
