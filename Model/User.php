<?php

namespace App\Model;

use App\Database\Connection;
use PDO;
use PDOException;

class User
{
    private $id;
    private $name;
    private $email;
    private $password;
    private $pdo;

    public function __construct($id = null, ?PDO $pdo = null)
    {
        $this->id  = $id;
        $this->pdo = $pdo ?? Connection::getInstance();
    }

    public function getId()           { return $this->id; }
    public function setId($id)        { $this->id = $id; }
    public function getName()         { return $this->name; }
    public function setName($name)    { $this->name = $name; }
    public function getEmail()        { return $this->email; }
    public function setEmail($email)  { $this->email = $email; }
    public function getPassword()           { return $this->password; }
    public function setPassword($password)  { $this->password = $password; }

    public function register()
    {
        try {
            $pre = $this->pdo->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?);');
            $pre->bindValue(1, $this->name);
            $pre->bindValue(2, $this->email);
            $pre->bindValue(3, $this->password);

            if ($pre->execute()) {
                return true;
            }

            error_log("Erro ao cadastrar usuário: " . implode(', ', $pre->errorInfo()));
            return false;
        } catch (PDOException $e) {
            error_log("Erro ao cadastrar usuário: " . $e->getMessage());
            return false;
        }
    }

    public function show($email)
    {
        try {
            $sql = $this->pdo->prepare("SELECT id, email FROM users WHERE email = :email;");
            $sql->bindValue(":email", $email);
            $sql->execute();
            return ($sql->rowCount() > 0);
        } catch (PDOException $e) {
            error_log("Erro ao buscar usuário: " . $e->getMessage());
            return false;
        }
    }

    public function showCustomers()
    {
        try {
            $sql  = "SELECT name, email FROM users WHERE profile = 'user' ORDER BY name ASC;";
            $data = $this->pdo->query($sql);

            if ($data) {
                return $data->fetchAll(PDO::FETCH_ASSOC);
            }
            return [];
        } catch (PDOException $e) {
            error_log("Erro ao listar clientes: " . $e->getMessage());
            return [];
        }
    }

    public function all()
    {
        try {
            $sql  = 'SELECT name, email FROM users ORDER BY name ASC;';
            $data = $this->pdo->query($sql);

            if ($data) {
                return $data->fetchAll(PDO::FETCH_ASSOC);
            }
            return [];
        } catch (PDOException $e) {
            error_log("Erro ao listar usuários: " . $e->getMessage());
            return [];
        }
    }
}
