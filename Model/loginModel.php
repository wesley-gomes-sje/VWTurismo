<?php
require_once './connection.php';
class Login
{
    private $email;
    private $password;
    public function __construct() {}
    
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
        if(session_status() !== PHP_SESSION_ACTIVE){
            session_start();
        }
        
        $connection = new connection();
        $pdo = $connection->connect();

        if (!$pdo) {
            error_log("Falha ao conectar ao banco de dados.");
            return false;
        }

        try {

            $sql = $pdo->prepare("SELECT *
            FROM users 
            WHERE email = :email;");
            $sql->bindValue(":email", $email);
            $sql->execute();

            if ($sql->rowCount() > 0) {
                $query = $sql->fetchAll(PDO::FETCH_ASSOC);
                $hashedPassword = $query[0]['password'];
                
                if (password_verify($password, $hashedPassword)) {
                    $_SESSION['idUser'] = $query[0]['id'];
                    $_SESSION['email'] = $query[0]['email'];
                    $_SESSION['profile'] = $query[0]['profile'];
                    $_SESSION['name'] = $query[0]['name'];
                    return true;
                }
            }
            error_log("Usuário ou senha incorretos.");
            return false;
        } catch (PDOException $e) {
            echo "Erro ao realizar o login: " . $e->getMessage();
            return false;
        }
    }
}
