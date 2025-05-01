<?php
require_once './connection.php';

class Vehicle
{

    private $id;
    private $brand;
    private $model;
    private $plate;
    private $year;
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
    public function getBrand()
    {
        return $this->brand;
    }
    public function setBrand($brand)
    {
        $this->brand = $brand;
    }
    public function getModel()
    {
        return $this->model;
    }
    public function setModel($model)
    {
        $this->model = $model;
    }
    public function getPlate()
    {
        return $this->plate;
    }
    public function setPlate($plate)
    {
        $this->plate = $plate;
    }
    public function getYear()
    {
        return $this->year;
    }
    public function setYear($year)
    {
        $this->year = $year;
    }

    public function register()
    {
        try {
            $sql = 'INSERT INTO vehicles (brand,model,plate,year) VALUES (?,?,?,?);';
            $pre = $this->pdo->prepare($sql);
            $pre->bindValue(1, $this->brand);
            $pre->bindValue(2, $this->model);
            $pre->bindValue(3, $this->plate);
            $pre->bindValue(4, $this->year);
            
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
            $sql = 'SELECT id, brand,model,plate, year FROM vehicles order by year ASC;';
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
