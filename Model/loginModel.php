<?php
require_once './conexao.php';
class Login
{
    private $emailUsuario;
    private $senha;
    public function __construct()
    { 
        
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
    public function login($emailUsuario, $senha)
    {
        $conexao = new conexao();

        try {
            
            $con = new PDO($conexao->dsn, $conexao->user, $conexao->pass);
            $sql = $con->prepare("SELECT idUsuario,emailUsuario,senha,status,nomeUsuario FROM usuario WHERE emailUsuario=:emailUsuario AND senha=:senha;");
            $sql->bindValue(":emailUsuario", $emailUsuario);
            $sql->bindValue(":senha", $senha);
            $sql->execute();
            if ($sql->rowCount() > 0) {
                $query = $sql->fetchAll(PDO::FETCH_ASSOC);
                $_SESSION['idUsuario'] = $query[0]['idUsuario'];
                $_SESSION['emailUsuario'] = $query[0]['emailUsuario'];
                $_SESSION['senha'] = $query[0]['senha'];
                $_SESSION['status'] = $query[0]['status'];
                $_SESSION['nomeUsuario'] = $query[0]['nomeUsuario'];
                return true;
            } else {
                unset($_SESSION['idUsuario']);
                unset($_SESSION['emailUsuario']);
                unset($_SESSION['senha']);
                unset($_SESSION['status']);
                unset($_SESSION['nomeUsuario']);
                return false;
            }
        } catch (PDOException $e) { }
    }
}
