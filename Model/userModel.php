<?php
require_once './connection.php';
class User
{
    private $id;
    private $name;
    private $email;
    private $password;
    private $pdo;
    
    public function __construct($id = null)
    {
        $this->id = $id;
        $connection = new Connection();
        $this->pdo = $connection->connect();       
         
        if (!$this->pdo) {
            echo "Falha ao conectar ao banco de dados.";
            return false;
        }
    }
    
    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;
    }
    public function getName()
    {
        return $this->name;
    }
    public function setName($name)
    {
        $this->name = $name;
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
    
    public function register()
    {
        try {
            $sql = 'INSERT INTO users (name,email,password) VALUES (?,?,?);';
            $pre = $this->pdo->prepare($sql);
            $pre->bindValue(1, $this->name);
            $pre->bindValue(2, $this->email);
            $pre->bindValue(3, $this->password);
            
            if ($pre->execute()) {
                return true;
            } else {
                print_r($pre->errorInfo());
            }
        } catch (PDOException $errorRegister) {
            echo $errorRegister->getMessage();
            return false;
        }
    }
    
    public function show($email)
    {
        try {
            $sql =  $this->pdo->prepare("SELECT id,email FROM users WHERE email=:email;");
            $sql->bindValue(":email", $email);
            $sql->execute();
            return ($sql->rowCount() > 0);
        } catch (PDOException $e) { }
    }
    
    public function showCustomers()
    {
        try {
            $sql = 'SELECT name, email FROM users WHERE profile=user order by name ASC;';
            $data = $this->pdo->query($sql);
            if ($data) {
                return $data->fetchAll(PDO::FETCH_ASSOC);
            } else {
                return array('response' => 'erro');
            }
        } catch (PDOException $errorShowCustomers) {
            echo $errorShowCustomers->getMessage();
            return array('response' => 'erro');
        }
    }
    
    public function all()
    {
        try {
            $sql = 'SELECT name, email FROM users order by name ASC;';
            $data = $this->pdo->query($sql);
            if ($data) {
                return $data->fetchAll(PDO::FETCH_ASSOC);
            } else {
                return array('response' => 'erro');
            }
        } catch (PDOException $errorAll) {
            echo $errorAll->getMessage();
            return array('response' => 'erro');
        }
    }
}
