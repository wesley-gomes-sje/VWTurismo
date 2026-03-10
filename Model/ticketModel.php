<?php

namespace App\Model;

use App\Database\Connection;
use PDO;
use PDOException;

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

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Connection::getInstance();
    }

    public function getId()            { return $this->id; }
    public function setId($id)         { $this->id = $id; }
    public function getPassenger()     { return $this->passenger; }
    public function setPassenger($v)   { $this->passenger = $v; }
    public function getRoute()         { return $this->route; }
    public function setRoute($v)       { $this->route = $v; }
    public function getVehicle()       { return $this->vehicle; }
    public function setVehicle($v)     { $this->vehicle = $v; }
    public function getOrigin()        { return $this->origin; }
    public function setOrigin($v)      { $this->origin = $v; }
    public function getDestination()   { return $this->destination; }
    public function setDestination($v) { $this->destination = $v; }
    public function getPrice()         { return $this->price; }
    public function setPrice($v)       { $this->price = $v; }
    public function getDate()          { return $this->date; }
    public function setDate($v)        { $this->date = $v; }

    public function register()
    {
        try {
            $pre = $this->pdo->prepare(
                'INSERT INTO tickets (passenger, route, vehicle, price, date) VALUES (?, ?, ?, ?, ?);'
            );
            $pre->bindValue(1, $this->passenger);
            $pre->bindValue(2, $this->route);
            $pre->bindValue(3, $this->vehicle);
            $pre->bindValue(4, $this->price);
            $pre->bindValue(5, $this->date);

            if ($pre->execute()) {
                return true;
            }

            error_log("Erro ao registrar passagem: " . implode(', ', $pre->errorInfo()));
            return false;
        } catch (PDOException $e) {
            error_log("Erro ao registrar passagem: " . $e->getMessage());
            return false;
        }
    }

    public function show($id)
    {
        try {
            $sql = 'SELECT t.date AS date, t.price AS price, co.name AS origin, cd.name AS destination, r.distance AS distance
                    FROM tickets t
                    INNER JOIN routes r ON t.route = r.id
                    INNER JOIN cities co ON r.origin = co.id
                    INNER JOIN cities cd ON r.destination = cd.id
                    INNER JOIN users u ON t.passenger = u.id
                    WHERE t.id = ?;';
            $pre = $this->pdo->prepare($sql);
            $pre->bindValue(1, $id);

            if ($pre->execute()) {
                return $pre->fetch(PDO::FETCH_ASSOC);
            }

            error_log("Erro ao buscar passagem: " . implode(', ', $pre->errorInfo()));
            return false;
        } catch (PDOException $e) {
            error_log("Erro ao buscar passagem: " . $e->getMessage());
            return false;
        }
    }

    public function all()
    {
        try {
            $sql  = 'SELECT u.name AS name, t.date AS date, t.price AS price,
                           co.name AS origin, cd.name AS destination, r.distance AS distance,
                           v.brand AS brand, v.model AS model, v.plate AS plate
                    FROM tickets t
                    INNER JOIN routes r ON t.route = r.id
                    INNER JOIN cities co ON r.origin = co.id
                    INNER JOIN cities cd ON r.destination = cd.id
                    INNER JOIN users u ON t.passenger = u.id
                    INNER JOIN vehicles v ON t.vehicle = v.id
                    ORDER BY t.date DESC;';
            $data = $this->pdo->query($sql);

            if ($data) {
                return $data->fetchAll(PDO::FETCH_ASSOC);
            }
            return [];
        } catch (PDOException $e) {
            error_log("Erro ao listar passagens: " . $e->getMessage());
            return [];
        }
    }

    public function showTicketsByPassenger($passenger)
    {
        try {
            $sql = 'SELECT t.date AS date, t.price AS price, v.brand AS brand, v.model AS model,
                           v.plate AS plate, co.name AS origin, cd.name AS destination, r.distance AS distance
                    FROM tickets t
                    INNER JOIN routes r ON t.route = r.id
                    INNER JOIN cities co ON r.origin = co.id
                    INNER JOIN cities cd ON r.destination = cd.id
                    INNER JOIN users u ON t.passenger = u.id
                    INNER JOIN vehicles v ON t.vehicle = v.id
                    WHERE t.passenger = ?;';
            $pre = $this->pdo->prepare($sql);
            $pre->bindValue(1, $passenger);

            if ($pre->execute()) {
                return $pre->fetchAll(PDO::FETCH_ASSOC);
            }

            error_log("Erro ao buscar passagens do passageiro: " . implode(', ', $pre->errorInfo()));
            return false;
        } catch (PDOException $e) {
            error_log("Erro ao buscar passagens do passageiro: " . $e->getMessage());
            return false;
        }
    }

    public function calculatePrice($distance)
    {
        return $distance * 0.5;
    }
}
