<?php
require_once './Model/User.php';
require_once './View/cadUsuarioView.php';

class userController
{
    private $userModel;
    private $userView;
    public function __construct()
    {
        $this->userModel = new User();
        $this->userView = new menuView();
    }
    
    public function open()
    {
        $data = $this->userModel->all();
        return $this->userView->custormers($data);
    }
    
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

        $this->userModel->setName($name);
        $this->userModel->setEmail($email);
        $this->userModel->setPassword($hashedPassword);
        
        if(!$this->userModel->register()){
             $this->fillFields('Erro ao cadastrar usuário.');
             return;
        }
        
        $this->fillFields('Usuario cadastrado.');
    }
    
    public function fillFields($message = '')
    {
        $userView = new cadUsuarioView();
        $userView->formRegister($message);
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
