<?php

class CreateCitiesTable
{
    public static function up($pdo)
    {
        $sql = "CREATE TABLE IF NOT EXISTS cities (
            id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
            name VARCHAR(100) NOT NULL,
            status TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        try {
            $pdo->exec($sql);
            echo "Tabela 'cities' criada com sucesso.\n";
        } catch (PDOException $e) {
            echo "Erro ao criar a tabela 'cities': " . $e->getMessage() . "\n";
        }
    }
}
