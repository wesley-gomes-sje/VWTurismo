<?php

require_once './conexao.php';

class Lista
{
    public function __construct()
    { }
    public function listaPassagem($idUsuario)
    {
        $conexao = new conexao();
        try {
            $con = new PDO($conexao->dsn, $conexao->user, $conexao->pass);
            $sql = $con->prepare("SELECT p.data AS data, u.nomeUsuario as nome, co.nomeCidade AS cidadeOrigem,cd.nomeCidade AS cidadeDestino , o.nomeOnibus AS onibus,o.placa AS placa,t.preco AS preco
            FROM passagem p 
            INNER JOIN usuario u ON p.idUsuario=u.idUsuario
            INNER JOIN trajeto t ON p.idTrajeto=t.idTrajeto
            INNER JOIN cidade co ON t.cidadeOrigem=co.idCidade
            INNER JOIN cidade cd ON t.cidadeDestino=cd.idCidade
            INNER JOIN onibus o  ON p.idOnibus=o.idOnibus
            WHERE p.idUsuario=:idUsuario;
            ");
            $sql->bindValue(":idUsuario", $idUsuario);
            $sql->execute();

            if ($sql->rowCount() > 0) {
                $query = $sql->fetchAll(PDO::FETCH_ASSOC);
                $_SESSION['data'] = $query[0]['data'];
                $_SESSION['nome'] = $query[0]['nome'];
                $_SESSION['cidadeOrigem'] = $query[0]['cidadeOrigem'];
                $_SESSION['cidadeDestino'] = $query[0]['cidadeDestino'];
                $_SESSION['onibus'] = $query[0]['onibus'];
                $_SESSION['placa'] = $query[0]['placa'];
                $_SESSION['preco'] = $query[0]['preco'];
                return true;
            } else {
                unset($_SESSION['data']);
                unset($_SESSION['cidadeOrigem']);
                unset($_SESSION['cidadeDestino']);
                unset($_SESSION['onibus']);
                unset($_SESSION['placa']);
                unset($_SESSION['preco']);
                return false;
            }
            
        } catch (PDOException $e) { }
    }
}
