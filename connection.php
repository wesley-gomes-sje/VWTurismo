<?php

require 'vendor/autoload.php';

class Connection {
    private $dsn;
    private $user;
    private $pass;

    public function __construct() {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
        $dotenv->load();

        $this->dsn = 'mysql:host=' . getenv('MYSQL_DB_HOST') . ';dbname=' . getenv('MYSQL_DATABASE');
        $this->user = getenv('MYSQL_USER');
        $this->pass = getenv('MYSQL_PASSWORD');
    }

    public function connect() {
        try {
            $pdo = new PDO($this->dsn, $this->user, $this->pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "Conexão bem-sucedida!";
            return $pdo;
        } catch (PDOException $e) {
            echo "Erro de conexão: " . $e->getMessage();
            return null;
        }
    }
}
