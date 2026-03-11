<?php

class CreateRoutesTable
{
    public static function up($pdo)
    {

        $sql = "CREATE TABLE IF NOT EXISTS routes(
            id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
            origin INT NOT NULL,
            destination INT NOT NULL,
            distance INT NOT NULL,
            status TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (origin) REFERENCES cities(id),
            FOREIGN KEY (destination) REFERENCES cities(id)
        )";
        try {
            $pdo->exec($sql);
            echo "Tabela 'routes' criada com sucesso.\n";
        } catch (PDOException $e) {
            echo "Erro ao criar a tabela 'routes': " . $e->getMessage() . "\n";
        }
    }
}
