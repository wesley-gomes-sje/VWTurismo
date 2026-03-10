<?php

namespace App\Controller;

use App\Model\Login;
use App\View\cadUsuarioView;
use App\View\menuView;

class loginController
{
    private $loginModel;
    private $menuView;
    private $userView;

    public function __construct()
    {
        $this->loginModel = new Login();
        $this->menuView   = new menuView();
        $this->userView   = new cadUsuarioView();
    }

    public function login()
    {
        $email    = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $this->sanitizeString($_POST['password'] ?? '');

        if (!$email || !$password) {
            $this->fillLogin('Usuário e senha são obrigatórios.');
            return;
        }

        $this->loginModel->setEmail($email);
        $this->loginModel->setPassword($password);

        $user = $this->loginModel->login(
            $this->loginModel->getEmail(),
            $this->loginModel->getPassword()
        );

        if (!$user) {
            $this->fillLogin('Usuario ou senha invalido.');
            return;
        }

        session_regenerate_id(true);
        $_SESSION['idUser']  = $user['id'];
        $_SESSION['email']   = $user['email'];
        $_SESSION['profile'] = $user['profile'];
        $_SESSION['name']    = $user['name'];

        if ($_SESSION['profile'] === 'user') {
            return $this->menuView->customer();
        }

        return $this->menuView->admin();
    }

    public function fillLogin($message = '')
    {
        $this->userView->formLogin($message);
    }

    private function sanitizeString(?string $string): string
    {
        return htmlspecialchars(strip_tags($string ?? ''), ENT_QUOTES, 'UTF-8');
    }
}
