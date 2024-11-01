<?php
require_once './connection.php';
class Route
{
    private $id;
    private $origin;
    private $destination;
    private $distance;
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

    public function getOrigin()
    {
        return $this->origin;
    }
    public function setOrigin($origin)
    {
        $this->origin = $origin;
    }
    public function getDestination()
    {
        return $this->destination;
    }
    public function setDestination($destination)
    {
        $this->destination = $destination;
    }
    public function getDistance()
    {
        return $this->distance;
    }
    public function setDistance($distance)
    {
        $this->distance = $distance;
    }
    
    public function register()
    {
        try {
            $sql = 'INSERT INTO routes (origin,destination,distance) VALUES (?,?,?);';
            $pre = $this->pdo->prepare($sql);
            $pre->bindValue(1, $this->origin);
            $pre->bindValue(2, $this->destination);
            $pre->bindValue(3, $this->distance);
            
            if ($pre->execute()) {
                return true;
            } else {
                print_r($pre->errorInfo());
            }
        } catch (PDOException $erroRegister) {
            echo $erroRegister->getMessage();
            return false;
        }
    }
    
    public function all()
    {
        try {
            $sql = 'SELECT cO.name AS cityOrigin,cD.name AS cityDestination,r.distance AS distance  FROM  cities cO  INNER JOIN routes r ON cO.id=r.origin INNER JOIN cities cD ON cD.id=t.destination;';
            $data = $this->pdo->query($sql);

            if ($data) {
                return $data->fetchAll(PDO::FETCH_ASSOC);
                
            } else {
                return array('response' => 'erro');
            }
        } catch (PDOException $errorAll) {
            echo $errorAll->getMessage();
            return false;
        }
    }

    public function allCityOrigin() // vou acessar a classe de city para buscar os dados
    {
        try {
            $sql = 'SELECT * FROM cities WHERE status=1 order by name ASC;';
            $data = $this->pdo->query($sql);
            if ($data) {
                return $data->fetchAll(PDO::FETCH_ASSOC);
            } else {
                return array('response' => 'erro');
            }
        } catch (PDOException $errorAllCityOrigin) {
            echo $errorAllCityOrigin->getMessage();
            return array('response' => 'erro');
        }
    }
}
