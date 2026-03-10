<?php

use App\Database\Connection;

class City
{
    private $id;
    private $name;
    private $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Connection::getInstance();
    }

    public function getId()      { return $this->id; }
    public function setId($id)   { $this->id = $id; }
    public function getName()    { return $this->name; }
    public function setName($n)  { $this->name = $n; }

    public function register()
    {
        try {
            $pre = $this->pdo->prepare('INSERT INTO cities (name) VALUES (?);');
            $pre->bindValue(1, $this->name);
            return $pre->execute() ? true : false;
        } catch (PDOException $e) {
            error_log('City::register — ' . $e->getMessage());
            return false;
        }
    }

    public function all()
    {
        try {
            $data = $this->pdo->query('SELECT * FROM cities WHERE status = 1 ORDER BY name ASC;');
            return $data ? $data->fetchAll(PDO::FETCH_ASSOC) : [];
        } catch (PDOException $e) {
            error_log('City::all — ' . $e->getMessage());
            return [];
        }
    }

    public function show($id)
    {
        try {
            $sql = $this->pdo->prepare('SELECT id, name FROM cities WHERE id = :id;');
            $sql->bindValue(':id', $id);
            $sql->execute();
            return $sql->rowCount() > 0 ? $sql->fetchAll(PDO::FETCH_ASSOC) : false;
        } catch (PDOException $e) {
            error_log('City::show — ' . $e->getMessage());
            return false;
        }
    }

    public function delete($id)
    {
        try {
            $pre = $this->pdo->prepare('UPDATE cities SET status = 0 WHERE id = :id;');
            $pre->bindValue(':id', $id);
            return $pre->execute() ? true : false;
        } catch (PDOException $e) {
            error_log('City::delete — ' . $e->getMessage());
            return false;
        }
    }

    public function edit($id, $name)
    {
        try {
            $pre = $this->pdo->prepare('UPDATE cities SET name = :name WHERE id = :id;');
            $pre->bindValue(':id', $id);
            $pre->bindValue(':name', $name);
            return $pre->execute() ? true : false;
        } catch (PDOException $e) {
            error_log('City::edit — ' . $e->getMessage());
            return false;
        }
    }
}
