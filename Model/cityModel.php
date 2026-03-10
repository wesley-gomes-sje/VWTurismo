<?php

namespace App\Model;

use App\Database\Connection;
use PDO;
use PDOException;

class City
{
    private $id;
    private $name;
    private $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Connection::getInstance();
    }

    public function getId()      { return $this->id; }
    public function setId($id)   { $this->id = $id; }
    public function getName()    { return $this->name; }
    public function setName($name) { $this->name = $name; }

    public function register()
    {
        try {
            $pre = $this->pdo->prepare('INSERT INTO cities (name) VALUES (?);');
            $pre->bindValue(1, $this->name);

            if ($pre->execute()) {
                return true;
            }

            error_log("Erro ao registrar cidade: " . implode(', ', $pre->errorInfo()));
            return false;
        } catch (PDOException $e) {
            error_log("Erro ao registrar cidade: " . $e->getMessage());
            return false;
        }
    }

    public function all()
    {
        try {
            $sql  = 'SELECT * FROM cities WHERE status = 1 ORDER BY name ASC;';
            $data = $this->pdo->query($sql);

            if ($data) {
                return $data->fetchAll(PDO::FETCH_ASSOC);
            }
            return [];
        } catch (PDOException $e) {
            error_log("Erro ao listar cidades: " . $e->getMessage());
            return [];
        }
    }

    public function show($id)
    {
        try {
            $sql = $this->pdo->prepare("SELECT id, name FROM cities WHERE id = :id;");
            $sql->bindValue(":id", $id);
            $sql->execute();

            if ($sql->rowCount() > 0) {
                return $sql->fetchAll(PDO::FETCH_ASSOC);
            }
            return [];
        } catch (PDOException $e) {
            error_log("Erro ao buscar cidade: " . $e->getMessage());
            return false;
        }
    }

    public function delete($id)
    {
        try {
            $pre = $this->pdo->prepare('UPDATE cities SET status = 0 WHERE id = :id;');
            $pre->bindValue(":id", $id);

            if ($pre->execute()) {
                return true;
            }

            error_log("Erro ao excluir cidade: " . implode(', ', $pre->errorInfo()));
            return false;
        } catch (PDOException $e) {
            error_log("Erro ao excluir cidade: " . $e->getMessage());
            return false;
        }
    }

    public function edit($id, $name)
    {
        try {
            $pre = $this->pdo->prepare('UPDATE cities SET name = :name WHERE id = :id;');
            $pre->bindValue(":id", $id);
            $pre->bindValue(":name", $name);

            if ($pre->execute()) {
                return true;
            }

            error_log("Erro ao editar cidade: " . implode(', ', $pre->errorInfo()));
            return false;
        } catch (PDOException $e) {
            error_log("Erro ao editar cidade: " . $e->getMessage());
            return false;
        }
    }
}
