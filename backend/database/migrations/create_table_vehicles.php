<?php

class CreateVehiclesTable
{
    public static function up($pdo)
    {
        $sql = "CREATE TABLE IF NOT EXISTS vehicles(
            id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
            brand VARCHAR(100) NOT NULL,
            model VARCHAR(100) NOT NULL,
            plate VARCHAR(10) NOT NULL,
            year INT NOT NULL,
            status TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        try {
            $pdo->exec($sql);
            echo "Tabela 'vehicles' criada com sucesso.\n";
        } catch (PDOException $e) {
            echo "Erro ao criar a tabela 'vehicles': " . $e->getMessage() . "\n";
        }
    }
}
