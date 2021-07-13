<?php
require_once './conexao.php';
class Cliente{
    public function __construct()
    { }
    public function listCliente()
    {
        $conexao = new conexao();
        try {
            $con = new PDO($conexao->dsn, $conexao->user, $conexao->pass);
            $sql = 'SELECT * FROM usuario WHERE status <> 2 order by nomeUsuario ASC;';
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