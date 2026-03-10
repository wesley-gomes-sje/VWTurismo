<?php

use App\Database\Connection;

class Route
{
    private $id;
    private $origin;
    private $destination;
    private $distance;
    private $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Connection::getInstance();
    }

    public function getId()            { return $this->id; }
    public function setId($id)         { $this->id = $id; }
    public function getOrigin()        { return $this->origin; }
    public function setOrigin($v)      { $this->origin = $v; }
    public function getDestination()   { return $this->destination; }
    public function setDestination($v) { $this->destination = $v; }
    public function getDistance()      { return $this->distance; }
    public function setDistance($v)    { $this->distance = $v; }

    public function register()
    {
        try {
            $pre = $this->pdo->prepare('INSERT INTO routes (origin, destination, distance) VALUES (?, ?, ?);');
            $pre->bindValue(1, $this->origin);
            $pre->bindValue(2, $this->destination);
            $pre->bindValue(3, $this->distance);
            return $pre->execute() ? true : false;
        } catch (PDOException $e) {
            error_log('Route::register — ' . $e->getMessage());
            return false;
        }
    }

    public function all()
    {
        try {
            $sql = 'SELECT cO.name AS origin, cD.name AS destination, r.distance
                    FROM cities cO
                    INNER JOIN routes r  ON cO.id = r.origin
                    INNER JOIN cities cD ON cD.id = r.destination;';
            $data = $this->pdo->query($sql);
            return $data ? $data->fetchAll(PDO::FETCH_ASSOC) : false;
        } catch (PDOException $e) {
            error_log('Route::all — ' . $e->getMessage());
            return false;
        }
    }

    public function check($origin, $destination)
    {
        try {
            $pre = $this->pdo->prepare('SELECT id, distance FROM routes WHERE origin = ? AND destination = ?;');
            $pre->bindValue(1, $origin);
            $pre->bindValue(2, $destination);
            $pre->execute();
            return $pre->fetch(PDO::FETCH_ASSOC) ?: false;
        } catch (PDOException $e) {
            error_log('Route::check — ' . $e->getMessage());
            return false;
        }
    }
}
