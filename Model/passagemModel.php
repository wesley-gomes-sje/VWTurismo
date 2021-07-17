<?php
require_once 'conexao.php';

class Passagem{
    
    private $idPassagem;
    private $idUsuario;
    private $idOnibus;
    private $idTrajeto;
    private $cidadeOrigem;
    private $cidadeDestino;
    private $data;


    public function __construct()
    { }
    public function getidPassagem(){
        return $this->idPassagem;
    }
    public function setidPassagem($idPassagem)
    {
        $this->idPassagem = $idPassagem;
    }


    public function getidUsuario(){
        return $this->idUsuario;
    }
    public function setidUsuario($idUsuario)
    {
        $this->idUsuario = $idUsuario;
    }


    public function getidOnibus(){
        return $this->idOnibus;
    }
    public function setidOnibus($idOnibus)
    {
        $this->idOnibus = $idOnibus;
    }



    public function getidTrajeto(){
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
    public function getdata()
    {
        return $this->data;
    }
    public function setdata($data)
    {
        $this->data = $data;
    }

    public function listOnibus()
    {
        $conexao = new conexao();
        try {
            $con = new PDO($conexao->dsn, $conexao->user, $conexao->pass);
            $sql = 'SELECT idOnibus, nomeOnibus,placa FROM onibus order by nomeOnibus ASC;';
            if ($data = $con->query($sql)) {
                return $data->fetchAll(PDO::FETCH_ASSOC);
            } else {
                return array('resposta' => 'erro');
            }
        } catch (PDOException $errolistUsuario) {
            return array('resposta' => 'erro');
        }
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

    public function verificaTrajeto($cidadeOrigem,$cidadeDestino){
        $conexao = new conexao();
        try{
            $con = new PDO($conexao->dsn, $conexao->user, $conexao->pass);
            $sql = $con->prepare("SELECT idTrajeto,distancia,preco FROM trajeto WHERE cidadeOrigem=:cidadeOrigem AND cidadeDestino=:cidadeDestino;");
            $sql->bindValue(":cidadeOrigem", $cidadeOrigem);
            $sql->bindValue(":cidadeDestino", $cidadeDestino);
            $sql->execute();

            if ($sql->rowCount() > 0){
                $query = $sql->fetchAll(PDO::FETCH_ASSOC);
                $_SESSION['idTrajeto'] = $query[0]['idTrajeto'];
                $_SESSION['distancia'] = $query[0]['distancia'];
                $_SESSION['preco'] = $query[0]['preco'];
                return true;
            } else{
                unset($_SESSION['idTrajeto']);
                unset($_SESSION['distancia']);
                unset($_SESSION['preco']);
                return false;
            }
        }catch (PDOException $e) { }

    }

    public function cadPassagem(){
        
        $conexao = new conexao();

        try{
            $con = new PDO($conexao->dsn, $conexao->user, $conexao->pass);
            $sql = 'INSERT INTO passagem (idUsuario,idOnibus,idTrajeto,data) VALUES (?,?,?,?) ';
            $pre = $con->prepare($sql);
            $pre->bindValue(1, $this->idUsuario);
            $pre->bindValue(2, $this->idOnibus);
            $pre->bindValue(3, $this->idTrajeto);
            $pre->bindValue(4, $this->data);
            if ($pre->execute()) {
                return true;
            } else {
                print_r($pre->errorInfo());
            }
        }catch (PDOException $errocadPassagem) {
            echo $errocadPassagem->getMessage();
            return false;
        }

    }
    public function listPassagem($idUsuario){
        $conexao = new conexao();
        try{
            $con = new PDO($conexao->dsn, $conexao->user, $conexao->pass);
            $sql=$con->prepare("SELECT p.data AS data, co.nomeCidade AS cidadeOrigem,cd.nomeCidade AS cidadeDestino,t.distancia AS distancia,t.preco AS preco 
            FROM passagem p 
            INNER JOIN usuario u ON p.idUsuario=u.idUsuario
            INNER JOIN trajeto t ON p.idTrajeto=t.idTrajeto
            INNER JOIN cidade co ON t.cidadeOrigem=co.idCidade
            INNER JOIN cidade cd ON t.cidadeDestino=cd.idCidade
             WHERE p.idUsuario=:idUsuario order by p.idPassagem desc limit 1;");
             $sql->bindValue(":idUsuario", $idUsuario);
             $sql->execute();
             if ($sql->rowCount() > 0) {
                $query = $sql->fetchAll(PDO::FETCH_ASSOC);
                return $query;
            } else {
                return array('resposta' => 'erro');
            }
        }catch (PDOException $errolistPassagem) {
            echo $errolistPassagem->getMessage();
            return false;
        }
    }
    public function listPassagemTudo($idUsuario){
        $conexao = new conexao();
        try{
            $con = new PDO($conexao->dsn, $conexao->user, $conexao->pass);
            $sql=$con->prepare("SELECT p.data AS data, co.nomeCidade AS cidadeOrigem,cd.nomeCidade AS cidadeDestino,o.nomeOnibus AS nomeOnibus,o.placa AS placa,t.distancia AS distancia,t.preco AS preco 
            FROM passagem p 
            INNER JOIN usuario u ON p.idUsuario=u.idUsuario
            INNER JOIN trajeto t ON p.idTrajeto=t.idTrajeto
            INNER JOIN cidade co ON t.cidadeOrigem=co.idCidade
            INNER JOIN cidade cd ON t.cidadeDestino=cd.idCidade
            INNER JOIN onibus o  ON p.idOnibus=o.idOnibus
             WHERE p.idUsuario=:idUsuario order by p.data;");
             $sql->bindValue(":idUsuario", $idUsuario);
             $sql->execute();
             if ($sql->rowCount() > 0) {
                $query = $sql->fetchAll(PDO::FETCH_ASSOC);
                return $query;
            } else {
                return array('resposta' => 'erro');
            }
        }catch (PDOException $errolistPassagem) {
            echo $errolistPassagem->getMessage();
            return false;
        }
    }
    public function listTudo(){
        $conexao = new conexao();
        try {
            $con = new PDO($conexao->dsn, $conexao->user, $conexao->pass);
            $sql = 'SELECT p.data AS data,u.nomeUsuario AS nomeUsuario, co.nomeCidade AS cidadeOrigem,cd.nomeCidade AS cidadeDestino,t.distancia AS distancia, o.nomeOnibus AS nomeOnibus,o.placa AS placa, t.preco AS preco 
            FROM passagem p 
            INNER JOIN usuario u ON p.idUsuario=u.idUsuario
            INNER JOIN trajeto t ON p.idTrajeto=t.idTrajeto
            INNER JOIN cidade co ON t.cidadeOrigem=co.idCidade
            INNER JOIN cidade cd ON t.cidadeDestino=cd.idCidade
            INNER JOIN onibus o  ON p.idOnibus=o.idOnibus
            order by u.nomeUsuario desc;';

            if ($data = $con->query($sql)) {
                return $data->fetchAll(PDO::FETCH_ASSOC);
            } else {
                return array('resposta' => 'erro');
            }
        } catch (PDOException $errolistCidOrigem) {
            return array('resposta' => 'erro');
        }
    }
    
}
