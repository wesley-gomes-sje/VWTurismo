<?php
require_once './conexao.php';
class Trajeto
{
    private $idTrajeto;
    private $cidadeOrigem;
    private $cidadeDestino;
    private $distancia;
    public function __construct()
    { }
    public function getidTrajeto()
    {
        return $this->idTrajeto;
    }
    public function setidTrajeto($idTrajeto)
    {
        $this->idTrajeto = $idTrajeto;
    }

    public function getcidadeOrigem()
    {
        return $this->cidadeOrigem;
    }
    public function setcidadeOrigem($cidadeOrigem)
    {
        $this->cidadeOrigem = $cidadeOrigem;
    }
    public function getcidadeDestino()
    {
        return $this->cidadeDestino;
    }
    public function setcidadeDestino($cidadeDestino)
    {
        $this->cidadeDestino = $cidadeDestino;
    }
    public function getdistancia()
    {
        return $this->distancia;
    }
    public function setdistancia($distancia)
    {
        $this->distancia = $distancia;
    }
    public function listCidOrigem()
    {
        $conexao = new conexao();
        try {
            $con = new PDO($conexao->dsn, $conexao->user, $conexao->pass);
            $sql = 'SELECT * FROM cidade order by nomeCidade ASC;';

            if ($data = $con->query($sql)) {
                return $data->fetchAll(PDO::FETCH_ASSOC);
            } else {
                return array('resposta' => 'erro');
            }
        } catch (PDOException $errolistCidOrigem) {
            return array('resposta' => 'erro');
        }
    }
    /*public function listCidDestino($cidadeOrigem)
    {
        $conexao = new conexao();
        try {
            $con = new PDO($conexao->dsn, $conexao->user, $conexao->pass);
            $sql = 'SELECT idCidade,nomeCidade FROM cidade WHERE idCidade!=:cidadeOrigem order by nomeCidade ASC;';
            $sql->bindValue(":cidadeOrigem", $cidadeOrigem);
            $sql->execute();
            if ($data = $con->query($sql)) {
                return $data->fetchAll(PDO::FETCH_ASSOC);
            } else {
                return array('resposta' => 'erro');
            }
        } catch (PDOException $errolistCidDestino) {
            return array('resposta' => 'erro');
        }
    }*/
    public function cadTrajeto()
    {
        $conexao = new conexao();
        try {
            $con = new PDO($conexao->dsn, $conexao->user, $conexao->pass);
            $sql = 'INSERT INTO trajeto (cidadeOrigem,cidadeDestino,distancia) VALUES (?,?,?);';
            $pre = $con->prepare($sql);
            $pre->bindValue(1, $this->cidadeOrigem);
            $pre->bindValue(2, $this->cidadeDestino);
            $pre->bindValue(3, $this->distancia);
            if ($pre->execute()) {
                return true;
            } else {
                print_r($pre->errorInfo());
            }
        } catch (PDOException $erroCadTrajeto) {
            echo $erroCadTrajeto->getMessage();
            return false;
        }
    }
    public function listTrajeto()
    {
        $conexao = new conexao();
        try {
            $con = new PDO($conexao->dsn, $conexao->user, $conexao->pass);
            $sql = 'SELECT cO.nomeCidade AS cidadeOrigem,cD.nomeCidade AS cidadeDestino,t.distancia AS distancia FROM  cidade cO  INNER JOIN trajeto t ON cO.idCidade=t.cidadeOrigem INNER JOIN cidade cD ON cD.idCidade=t.cidadeDestino;';

            if ($data = $con->query($sql)) {
                return $data->fetchAll(PDO::FETCH_ASSOC);
                
            } else {
                return array('resposta' => 'erro');
            }
        } catch (PDOException $errolistTrajeto) {
            echo $errolistTrajeto->getMessage();
            return false;
        }
    }
}
