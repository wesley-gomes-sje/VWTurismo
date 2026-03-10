<?php

namespace App\Model;

use App\Database\Connection;
use PDO;
use PDOException;

class Vehicle
{
    private $id;
    private $brand;
    private $model;
    private $plate;
    private $year;
    private $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Connection::getInstance();
    }

    public function getId()          { return $this->id; }
    public function setId($id)       { $this->id = $id; }
    public function getBrand()       { return $this->brand; }
    public function setBrand($v)     { $this->brand = $v; }
    public function getModel()       { return $this->model; }
    public function setModel($v)     { $this->model = $v; }
    public function getPlate()       { return $this->plate; }
    public function setPlate($v)     { $this->plate = $v; }
    public function getYear()        { return $this->year; }
    public function setYear($v)      { $this->year = $v; }

    public function register()
    {
        try {
            $pre = $this->pdo->prepare('INSERT INTO vehicles (brand, model, plate, year) VALUES (?, ?, ?, ?);');
            $pre->bindValue(1, $this->brand);
            $pre->bindValue(2, $this->model);
            $pre->bindValue(3, $this->plate);
            $pre->bindValue(4, $this->year);
            return $pre->execute() ? true : false;
        } catch (PDOException $e) {
            error_log('Vehicle::register — ' . $e->getMessage());
            return false;
        }
    }

    public function all()
    {
        try {
            $data = $this->pdo->query('SELECT id, brand, model, plate, year FROM vehicles ORDER BY year ASC;');
            return $data ? $data->fetchAll(PDO::FETCH_ASSOC) : [];
        } catch (PDOException $e) {
            error_log('Vehicle::all — ' . $e->getMessage());
            return [];
        }
    }
}
