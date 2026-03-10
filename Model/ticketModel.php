<?php

use App\Database\Connection;

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
            return $pre->execute() ? true : false;
        } catch (PDOException $e) {
            error_log('Ticket::register — ' . $e->getMessage());
            return false;
        }
    }

    public function show($id)
    {
        try {
            $sql = 'SELECT t.date, t.price, co.name AS origin, cd.name AS destination, r.distance
                    FROM tickets t
                    INNER JOIN routes r  ON t.route = r.id
                    INNER JOIN cities co ON r.origin = co.id
                    INNER JOIN cities cd ON r.destination = cd.id
                    INNER JOIN users u   ON t.passenger = u.id
                    WHERE t.id = ?;';
            $pre = $this->pdo->prepare($sql);
            $pre->bindValue(1, $id);
            $pre->execute();
            return $pre->fetch(PDO::FETCH_ASSOC) ?: false;
        } catch (PDOException $e) {
            error_log('Ticket::show — ' . $e->getMessage());
            return false;
        }
    }

    public function all()
    {
        try {
            $sql = 'SELECT u.name, t.date, t.price, co.name AS origin, cd.name AS destination,
                           r.distance, v.brand, v.model, v.plate
                    FROM tickets t
                    INNER JOIN routes r   ON t.route = r.id
                    INNER JOIN cities co  ON r.origin = co.id
                    INNER JOIN cities cd  ON r.destination = cd.id
                    INNER JOIN users u    ON t.passenger = u.id
                    INNER JOIN vehicles v ON t.vehicle = v.id
                    ORDER BY t.date DESC;';
            $data = $this->pdo->query($sql);
            return $data ? $data->fetchAll(PDO::FETCH_ASSOC) : [];
        } catch (PDOException $e) {
            error_log('Ticket::all — ' . $e->getMessage());
            return [];
        }
    }

    public function showTicketsByPassenger($passenger)
    {
        try {
            $sql = 'SELECT t.date, t.price, v.brand, v.model, v.plate,
                           co.name AS origin, cd.name AS destination, r.distance
                    FROM tickets t
                    INNER JOIN routes r   ON t.route = r.id
                    INNER JOIN cities co  ON r.origin = co.id
                    INNER JOIN cities cd  ON r.destination = cd.id
                    INNER JOIN users u    ON t.passenger = u.id
                    INNER JOIN vehicles v ON t.vehicle = v.id
                    WHERE t.passenger = ?;';
            $pre = $this->pdo->prepare($sql);
            $pre->bindValue(1, $passenger);
            $pre->execute();
            return $pre->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Ticket::showTicketsByPassenger — ' . $e->getMessage());
            return false;
        }
    }

    public function calculatePrice(float $distance): float
    {
        return $distance * 0.5;
    }
}
