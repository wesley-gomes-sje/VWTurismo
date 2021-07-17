<?php
require_once './conexao.php';
class Cidade
{
    private $idCidade;
    private $nomeCidade;

    public function __construct()
    { }
    public function getidCidade()
    {
        return $this->idCidade;
    }
    public function setidCidade($idCidade)
    {
        $this->idCidade = $idCidade;
    }
    public function getnomeCidade()
    {
        return $this->nomeCidade;
    }
    public function setnomeCidade($nomeCidade)
    {
        $this->nomeCidade = $nomeCidade;
    }
    public function cadCidade()
    {
        $conexao = new conexao();
        try {
            $con = new PDO($conexao->dsn, $conexao->user, $conexao->pass);
            $sql = 'INSERT INTO cidade (nomeCidade) VALUES (?);';
            $pre = $con->prepare($sql);
            $pre->bindValue(1, $this->nomeCidade);
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
    public function listCidade()
    {
        $conexao = new conexao();
        try {
            $con = new PDO($conexao->dsn, $conexao->user, $conexao->pass);
            $sql = 'SELECT DISTINCT * FROM cidade WHERE status=1 order by nomeCidade ASC;';

            if ($data = $con->query($sql)) {
                return $data->fetchAll(PDO::FETCH_ASSOC);
            } else {
                return array('resposta' => 'erro');
            }
        } catch (PDOException $errolisCidade) {
            return array('resposta' => 'erro');
        }
    }
    public function listCidadeUnica($idCidade){
        $conexao = new conexao();
        try{
            $con = new PDO($conexao->dsn, $conexao->user, $conexao->pass);
            $sql = $con->prepare("SELECT idCidade,nomeCidade FROM cidade WHERE idCidade=:idCidade;");
            $sql->bindValue(":idCidade", $idCidade);
            $sql->execute();
            if ($sql->rowCount() > 0) {
                $query = $sql->fetchAll(PDO::FETCH_ASSOC);
                return $query;
            } else {
                return array('resposta' => 'erro');
            }

        } catch (PDOException $erro) {
          echo $erro->getMessage();
          return false;
    }
    }
    public function excluir($idCidade){
        $conexao = new conexao();
        try{
            $con = new PDO($conexao->dsn, $conexao->user, $conexao->pass);
            $sql ='UPDATE cidade SET status=0 WHERE idCidade=:id;';
            $pre = $con->prepare($sql);
            $pre->bindValue(":id", $idCidade);
            if($pre->execute()){
                return true;
            } else{
                print_r($pre->errorInfo());
                return false;
            }
        } catch (PDOException $erro) {
            echo $erro->getMessage();
            return false;
        }

    }
    public function editar($idCidade,$nomeCidade){
        $conexao = new conexao();
        try{
            $con = new PDO($conexao->dsn, $conexao->user, $conexao->pass);
            $sql ='UPDATE cidade SET nomeCidade=:nomeCidade WHERE idCidade=:idCidade;';
            $pre = $con->prepare($sql);
            $pre->bindValue(":idCidade", $idCidade);
            $pre->bindValue(":nomeCidade", $nomeCidade);
            if($pre->execute()){
                return true;
            } else{
                print_r($pre->errorInfo());
                return false;
            }
           
        } catch (PDOException $erro) {
          echo $erro->getMessage();
          return false;
    }
   
}
}