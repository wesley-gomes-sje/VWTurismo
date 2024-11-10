<?php
namespace Models;
class User
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function register($name, $email, $password)
    {
        $stmt  = $this->pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        return $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
    }

    public function show($email)
    {
        $stmt = $this->pdo->prepare("SELECT name, email FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function showCustomers()
    {
        $stmt = $this->pdo->prepare("SELECT name, email FROM users WHERE profile=user order by name ASC;");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
