<?php
session_start();
require_once './Model/loginModel.php';
require_once './View/cadUsuarioView.php';
require_once './View/menuView.php';
class loginController
{
    public function __construct() {}

    public function login()
    {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $this->sanitizeString($_POST['password'] ?? '');


        if (!$email || !$password) {

            $this->fillLogin('Usuário e senha são obrigatórios.');
            return;
        }

        $loginModel = new Login();
        $loginModel->setEmail($email);
        $loginModel->setPassword($password);

        $getEmail = $loginModel->getEmail();
        $getPassword = $loginModel->getPassword();

        $islogin = $loginModel->login($getEmail, $getPassword);
        if (!$islogin) {

            $this->fillLogin('Usuario ou senha invalido.');
            return;
        }

        $menuView = new menuView();
        
        if ($_SESSION['profile'] == "user") {
            return $menuView->customer();
        } 
        return $menuView->admin();
    }

    public function fillLogin($message = '')
    {
        $userView = new cadUsuarioView();
        $userView->formLogin($message);
    }

    private function sanitizeString(?string $string): string
    {
        return htmlspecialchars(strip_tags($string ?? ''), ENT_QUOTES, 'UTF-8');
    }
}
