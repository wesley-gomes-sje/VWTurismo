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
    public function setName($v)       { $this->name = $v; }
    public function getEmail()        { return $this->email; }
    public function setEmail($v)      { $this->email = $v; }
    public function getPassword()     { return $this->password; }
    public function setPassword($v)   { $this->password = $v; }

    public function register()
    {
        try {
            $pre = $this->pdo->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?);');
            $pre->bindValue(1, $this->name);
            $pre->bindValue(2, $this->email);
            $pre->bindValue(3, $this->password);
            return $pre->execute() ? true : false;
        } catch (PDOException $e) {
            error_log('User::register — ' . $e->getMessage());
            return false;
        }
    }

    public function show($email)
    {
        try {
            $sql = $this->pdo->prepare('SELECT id, email FROM users WHERE email = :email;');
            $sql->bindValue(':email', $email);
            $sql->execute();
            return $sql->rowCount() > 0;
        } catch (PDOException $e) {
            error_log('User::show — ' . $e->getMessage());
            return false;
        }
    }

    public function showCustomers()
    {
        try {
            $data = $this->pdo->query("SELECT name, email FROM users WHERE profile = 'user' ORDER BY name ASC;");
            return $data ? $data->fetchAll(PDO::FETCH_ASSOC) : [];
        } catch (PDOException $e) {
            error_log('User::showCustomers — ' . $e->getMessage());
            return [];
        }
    }

    public function all()
    {
        try {
            $data = $this->pdo->query("SELECT name, email FROM users ORDER BY name ASC;");
            return $data ? $data->fetchAll(PDO::FETCH_ASSOC) : [];
        } catch (PDOException $e) {
            error_log('User::all — ' . $e->getMessage());
            return [];
        }
    }
}
