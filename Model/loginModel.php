<?php
require_once './connection.php';
class Login
{
    private $email;
    private $password;
    public function __construct()
    { 
        
    }
    public function getEmail()
    {
        return $this->email;
    }
    public function setEmail($email)
    {
        $this->email = $email;
    }
    public function getPassword()
    {
        return $this->password;
    }
    public function setPassword($password)
    {
        $this->password = $password;
    }
    public function login($email, $password)
    {
        $connection = new connection();
        $pdo = $connection->connect();
        
        if (!$pdo) {
            echo "Falha ao conectar ao banco de dados.";
            return false;
        }

        try {
            
            $sql = $pdo->prepare("SELECT id,email,password,status,name FROM users WHERE email=:email AND password=:password;");
            $sql->bindValue(":email", $email);
            $sql->bindValue(":password", $password);
            $sql->execute();
            
            if ($sql->rowCount() > 0) {
                $query = $sql->fetchAll(PDO::FETCH_ASSOC);
                $_SESSION['idUser'] = $query[0]['id'];
                $_SESSION['email'] = $query[0]['email'];
                $_SESSION['password'] = $query[0]['password'];
                $_SESSION['status'] = $query[0]['status'];
                $_SESSION['name'] = $query[0]['name'];
                return true;
            } else {
                unset($_SESSION['idUser']);
                unset($_SESSION['email']);
                unset($_SESSION['password']);
                unset($_SESSION['status']);
                unset($_SESSION['name']);
                return false;
            }
        } catch (PDOException $e) { }
    }
}
