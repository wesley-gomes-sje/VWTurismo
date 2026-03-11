<?php

class CreateUsersTable
{
    public static function up($pdo)
    {
        $sql = "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(100) NOT NULL,
            profile ENUM('admin', 'user') DEFAULT 'user',
            status TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        try {
            $pdo->exec($sql);
            echo "Tabela 'users' criada com sucesso.\n";
        } catch (PDOException $e) {
            echo "Erro ao criar a tabela 'users': " . $e->getMessage() . "\n";
        }
    }
}
