<?php
require_once './conexao.php';
class Onibus
{

    private $idOnibus;
    private $nomeOnibus;
    private $placa;
    public function __construct()
    { }
    public function getidOnibus()
    {
        return $this->idOnibus;
    }
    public function setidOnibus($idOnibus)
    {
        $this->idOnibus = $idOnibus;
    }
    public function getnomeOnibus()
    {
        return $this->nomeOnibus;
    }
    public function setnomeOnibus($nomeOnibus)
    {
        $this->nomeOnibus = $nomeOnibus;
    }
    public function getplaca()
    {
        return $this->placa;
    }
    public function setplaca($placa)
    {
        $this->placa = $placa;
    }

    
    public function cadOnibus()
    {
        $conexao = new conexao();
        try {
            $con = new PDO($conexao->dsn, $conexao->user, $conexao->pass);
            $sql = 'INSERT INTO onibus (nomeOnibus,placa) VALUES (?,?);';
            $pre = $con->prepare($sql);
            $pre->bindValue(1, $this->nomeOnibus);
            $pre->bindValue(2, $this->placa);
            if ($pre->execute()) {
                return true;
            } else {
                print_r($pre->errorInfo());
            }
        } catch (PDOException $erroCadOnibus) {
            echo $erroCadOnibus->getMessage();
            return false;
        }
    }

    public function listOnibus()
    {
        $conexao = new conexao();
        try {
            $con = new PDO($conexao->dsn, $conexao->user, $conexao->pass);
            $sql = 'SELECT nomeOnibus,placa FROM onibus order by nomeOnibus ASC;';
            if ($data = $con->query($sql)) {
                return $data->fetchAll(PDO::FETCH_ASSOC);
            } else {
                return array('resposta' => 'erro');
            }
        } catch (PDOException $errolistUsuario) {
            return array('resposta' => 'erro');
        }
    }
    
}
