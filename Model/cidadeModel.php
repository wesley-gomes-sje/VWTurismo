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
            $sql = 'SELECT DISTINCT * FROM cidade order by nomeCidade ASC;';

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
