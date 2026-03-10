<?php

namespace App\Model;

use App\Database\Connection;
use PDO;
use PDOException;

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

            if ($pre->execute()) {
                return true;
            }

            error_log("Erro ao registrar rota: " . implode(', ', $pre->errorInfo()));
            return false;
        } catch (PDOException $e) {
            error_log("Erro ao registrar rota: " . $e->getMessage());
            return false;
        }
    }

    public function all()
    {
        try {
            $sql  = 'SELECT cO.name AS origin, cD.name AS destination, r.distance AS distance
                    FROM cities cO
                    INNER JOIN routes r ON cO.id = r.origin
                    INNER JOIN cities cD ON cD.id = r.destination;';
            $data = $this->pdo->query($sql);

            if ($data) {
                return $data->fetchAll(PDO::FETCH_ASSOC);
            }
            return [];
        } catch (PDOException $e) {
            error_log("Erro ao listar rotas: " . $e->getMessage());
            return [];
        }
    }

    public function check($origin, $destination)
    {
        try {
            $pre = $this->pdo->prepare('SELECT id, distance FROM routes WHERE origin = ? AND destination = ?;');
            $pre->bindValue(1, $origin);
            $pre->bindValue(2, $destination);

            if ($pre->execute()) {
                return $pre->fetch(PDO::FETCH_ASSOC);
            }

            error_log("Erro ao verificar rota: " . implode(', ', $pre->errorInfo()));
            return false;
        } catch (PDOException $e) {
            error_log("Erro ao verificar rota: " . $e->getMessage());
            return false;
        }
    }
}
