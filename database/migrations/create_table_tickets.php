<?php

class CreateTicketsTable
{
    public static function up($pdo)
    {

        $sql = "CREATE TABLE IF NOT EXISTS tickets (
            id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
            passenger INT NOT NULL,
            route INT NOT NULL,
            vehicle INT NOT NULL,
            title VARCHAR(100) NOT NULL,
            date DATE NOT NULL,
            status TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (passenger) REFERENCES users(id),
            FOREIGN KEY (route) REFERENCES routes(id),
            FOREIGN KEY (vehicle) REFERENCES vehicles(id)
        )";
        try {
            $pdo->exec($sql);
            echo "Tabela 'tickets' criada com sucesso.\n";
        } catch (PDOException $e) {
            echo "Erro ao criar a tabela 'tickets': " . $e->getMessage() . "\n";
        }
    }
}