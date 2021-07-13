<?php
require_once 'conexao.php';

class Passagem{
    
    private $idPassagem;
    private $idUsuario;
    private $idOnibus;
    private $idTrajeto;
    private $preco;
    private $cidadeOrigem;
    private $cidadeDestino;


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


    public function getpreco(){
        return $this->preco;
    }
    public function setpreco($preco)
    {
        $this->preco = $preco;
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
