<?php
require 'Conexao.php';

$conexao = new Conexao();
$pdo = $conexao->conectar();

if ($pdo) {
    try {
        $sql = "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            password VARCHAR(100) NOT NULL,
            status INT(1) NOT NULL DEFAULT 1
        )";

        $pdo->exec($sql);
        echo "Tabela criada com sucesso!";
    } catch (PDOException $e) {
        echo "Erro ao criar a tabela: " . $e->getMessage();
    }
} else {
    echo "Não foi possível estabelecer conexão com o banco de dados.";
}
