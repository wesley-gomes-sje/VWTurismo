<?php
require_once './connection.php';
class City
{
    private $id;
    private $name;
    private $pdo;

    public function __construct()
    {
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
    public function register()
    {
        try {
            $sql = 'INSERT INTO cities (name) VALUES (?);';
            $pre = $this->pdo->prepare($sql);
            $pre->bindValue(1, $this->name);
            
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
    
    public function all()
    {
        try {
            $sql = 'SELECT  * FROM cities WHERE status= 1 order by name ASC;';
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
    
    public function show($id)
    {
        try {
            $sql = $this->pdo->prepare("SELECT id,name FROM cities WHERE id=:id;");
            $sql->bindValue(":id", $id);
            $sql->execute();
            if ($sql->rowCount() > 0) {
                $query = $sql->fetchAll(PDO::FETCH_ASSOC);
                return $query;
            } else {
                return array('response' => 'erro');
            }
        } catch (PDOException $errorShow) {
            echo $errorShow->getMessage();
            return false;
        }
    }
    
    public function delete($id)
    {
        try {
            $sql = 'UPDATE cities SET status=0 WHERE id=:id;';
            $pre = $this->pdo->prepare($sql);
            $pre->bindValue(":id", $id);
            if ($pre->execute()) {
                return true;
            } else {
                print_r($pre->errorInfo());
                return false;
            }
        } catch (PDOException $errorDelete) {
            echo $errorDelete->getMessage();
            return false;
        }
    }
    
    public function edit($id, $name)
    {
        try {
            $sql = 'UPDATE cities SET name= :name WHERE id= :id;';
            $pre = $this->pdo->prepare($sql);
            $pre->bindValue(":id", $id);
            $pre->bindValue(":name", $name);
            if ($pre->execute()) {
                return true;
            } else {
                print_r($pre->errorInfo());
                return false;
            }
        } catch (PDOException $errorEdit) {
            echo $errorEdit->getMessage();
            return false;
        }
    }
}
