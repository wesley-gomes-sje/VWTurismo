<?php
require_once './conexao.php';
class Usuario
{
    private $idUsuario;
    private $nomeUsuario;
    private $emailUsuario;
    private $senha;
    public function __construct($idUsuario = null)
    {
        $this->idUsuario = $idUsuario;
    }
    public function getidUsuario()
    {
        return $this->idUsuario;
    }
    public function setidUsuairo($idUsuario)
    {
        $this->idUsuario = $idUsuario;
    }

    public function getnomeUsuario()
    {
        return $this->nomeUsuario;
    }
    public function setnomeUsuario($nomeUsuario)
    {
        $this->nomeUsuario = $nomeUsuario;
    }
    public function getemailUsuario()
    {
        return $this->emailUsuario;
    }
    public function setemailUsuario($emailUsuario)
    {
        $this->emailUsuario = $emailUsuario;
    }
    public function getsenha()
    {
        return $this->senha;
    }
    public function setsenha($senha)
    {
        $this->senha = $senha;
    }
    public function cadUsuario()
    {
        $conexao = new conexao();
        try {
            $con = new PDO($conexao->dsn, $conexao->user, $conexao->pass);
            $sql = 'INSERT INTO usuario (nomeUsuario,emailUsuario,senha) VALUES (?,?,?);';
            $pre = $con->prepare($sql);
            $pre->bindValue(1, $this->nomeUsuario);
            $pre->bindValue(2, $this->emailUsuario);
            $pre->bindValue(3, $this->senha);
            if ($pre->execute()) {
                return true;
            } else {
                print_r($pre->errorInfo());
            }
        } catch (PDOException $erroCadastrar) {
            echo $erroCadastrar->getMessage();
            return false;
        }
    }
    public function verUsuario($emailUsuario)
    {
        try {
            $conexao = new conexao();
            $con = new PDO($conexao->dsn, $conexao->user, $conexao->pass);
            $sql = $con->prepare("SELECT idUsuario,emailUsuario FROM usuario WHERE emailUsuario=:emailUsuario;");
            $sql->bindValue(":emailUsuario", $emailUsuario);
            $sql->execute();
            return ($sql->rowCount() > 0);
        } catch (PDOException $e) { }
    }
    public function listUsuario()
    {
        $conexao = new conexao();
        try {
            $con = new PDO($conexao->dsn, $conexao->user, $conexao->pass);
            $sql = 'SELECT nomeUsuario, emailUsuario FROM usuario order by nomeUsuario ASC;';
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
