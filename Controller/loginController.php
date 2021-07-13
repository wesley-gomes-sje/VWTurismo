<?php
session_start();
require_once './Model/loginModel.php';
require_once './View/cadUsuarioView.php';
require_once './View/menuView.php';
class loginController
{
    public function __construct()
    { }

    public function preencherLogin($mensagem = '')
    {
        $usuarioView = new cadUsuarioView();
        $usuarioView->formLogin($mensagem);
    }

    public function login()
    {
        $emailUsuario = filter_input(INPUT_POST, 'emailUsuario', FILTER_SANITIZE_EMAIL);
        $senha = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_STRING);

        if (!$emailUsuario || !$senha) {

            return $this->preencherLogin('Usuario e senha são obrigatórios.');
        }

        $loginModel = new Login();
        $loginModel->setemailUsuario($emailUsuario);
        $loginModel->setsenha($senha);
        $getEmail = $loginModel->getemailUsuario();
        $getSenha = $loginModel->getsenha();
        $islogin = $loginModel->login($getEmail, $getSenha);

        if ($islogin == false) {

            return $this->preencherLogin('Usuario ou senha invalido.');
        }

        if ($_SESSION['status'] == "1") {
           $menuView = new menuView();
           return $menuView->telaCliente();

        } else if ($_SESSION['status'] == "2") {
            $menuView = new menuView();
            return $menuView->telaAdm();
        }
    }
}
