<?php
require_once './connection.php';

class Ticket
{

    private $id;
    private $passenger;
    private $route;
    private $vehicle;
    private $origin;
    private $destination;
    private $price;
    private $date;
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

    public function getPassenger()
    {
        return $this->passenger;
    }
    public function setPassenger($passenger)
    {
        $this->passenger = $passenger;
    }

    public function getRoute()
    {
        return $this->route;
    }
    public function setRoute($route)
    {
        $this->route = $route;
    }

    public function getVehicle()
    {
        return $this->vehicle;
    }
    public function setVehicle($vehicle)
    {
        $this->vehicle = $vehicle;
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

    public function getPrice()
    {
        return $this->price;
    }
    public function setPrice($price)
    {
        $this->price = $price;
    }

    public function getDate()
    {
        return $this->date;
    }
    public function setDate($date)
    {
        $this->date = $date;
    }

    public function register()
    {

        try {
            $sql = 'INSERT INTO tickets (passenger,route,vehicle,price,date) VALUES (?,?,?,?,?);';
            $pre = $this->pdo->prepare($sql);
            $pre->bindValue(1, $this->passenger);
            $pre->bindValue(2, $this->route);
            $pre->bindValue(3, $this->vehicle);
            $pre->bindValue(4, $this->price);
            $pre->bindValue(5, $this->date);
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

    public function show($id)
    {
        try {
            $sql = 'SELECT  t.date AS date, t.price AS price, co.name AS origin, cd.name AS destination, r.distance AS distance
            FROM tickets t
            INNER JOIN routes r ON t.route=r.id
            INNER JOIN cities co ON r.origin=co.id
            INNER JOIN cities cd ON r.destination=cd.id
            INNER JOIN users u ON t.passenger=u.id           
            WHERE id = ?;';
            $pre = $this->pdo->prepare($sql);
            $pre->bindValue(1, $id);
            if ($pre->execute()) {
                return $pre->fetch(PDO::FETCH_ASSOC);
            } else {
                print_r($pre->errorInfo());
            }
        } catch (PDOException $errorShow) {
            echo $errorShow->getMessage();
            return false;
        }
    }

    public function all()
    {
        try {
            $sql = 'SELECT  t.date AS date, t.price AS price, co.name AS origin, cd.name AS destination, r.distance AS distance
            FROM tickets t
            INNER JOIN routes r ON t.route=r.id
            INNER JOIN cities co ON r.origin=co.id
            INNER JOIN cities cd ON r.destination=cd.id
            INNER JOIN users u ON t.passenger=u.id
            ORDER BY t.date DESC;';
            $data = $this->pdo->query($sql);

            if ($data) {
                return $data->fetchAll(PDO::FETCH_ASSOC);
            } else {
                return array('resposta' => 'erro');
            }
        } catch (PDOException $errorAll) {
            return array('resposta' => 'erro');
        }
    }

    public function showTicketsByPassenger($passenger)
    {
        try {
            $sql = 'SELECT  t.date AS date, t.price AS price, co.name AS origin, cd.name AS destination, r.distance AS distance
            FROM tickets t
            INNER JOIN routes r ON t.route=r.id
            INNER JOIN cities co ON r.origin=co.id
            INNER JOIN cities cd ON r.destination=cd.id
            INNER JOIN users u ON t.passenger=u.id           
            WHERE t.passenger = ?;';
            $pre = $this->pdo->prepare($sql);
            $pre->bindValue(1, $passenger);
            if ($pre->execute()) {
                return $pre->fetchAll(PDO::FETCH_ASSOC);
            } else {
                print_r($pre->errorInfo());
            }
        } catch (PDOException $errorShowTicketsByPassenger) {
            echo $errorShowTicketsByPassenger->getMessage();
            return false;
        }
    }
}
