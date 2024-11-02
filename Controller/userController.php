<?php
require_once './Model/userModel.php';
require_once './View/cadUsuarioView.php';

class userController
{
    public function __construct()
    {}
    
    public function register()
    {
        $name = $this->sanitizeString($_POST['name'] ?? '');
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $this->sanitizeString($_POST['password'] ?? '');
        $confirmPassword = $this->sanitizeString($_POST['confirmPassword'] ?? '');

        if (!$name || !$email || !$password || !$confirmPassword) {
             $this->fillFields('Preencha todos os campos!');
            return;
        }
           
        if (!$this->checkPassword($password, $confirmPassword)) {
             $this->fillFields('Senhas não conferem.');
             return;
        }
        $hashedPassword = $this->hashPassword($password);
        $userModel = new User();
        $userModel->setName($name);
        $userModel->setEmail($email);
        $userModel->setPassword($hashedPassword);
        
        if($userModel->register()){
             $this->fillFields('Usuario cadastrado.');
             return;
        }
        
        $this->fillFields('Usuario cadastrado.');
    }
    
    public function fillFields($mensagem = '')
    {
        $usuarioView = new cadUsuarioView();
        $usuarioView->formulario($mensagem);
    }
    
    private function checkPassword(string $password, string $confirmPassword): bool
    {
        return $password === $confirmPassword;
    }
    
    private function sanitizeString(?string $string): string
    {
        return htmlspecialchars(strip_tags($string ?? ''), ENT_QUOTES, 'UTF-8');
    }
    
    private function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

}
