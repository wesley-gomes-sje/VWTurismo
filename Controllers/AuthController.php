<?php

namespace Controllers;

use Firebase\JWT\JWT;
use Models\User;

class AuthController
{
    private $userModel;

    public function __construct($pdo)
    {
        $this->userModel = new User($pdo);
    }

    public function register($name, $email, $password, $confirm_password)
    {
        if ($this->userModel->show($email)) {
            return json_encode(["message" => "Email already exists"]);
        }
        
        if ($password !== $confirm_password) {
            return json_encode(["message" => "Passwords do not match"]);
        }
        
        if (!$this->userModel->register($name, $email, $password)) {
            return json_encode(["message" => "Error on register"]);
        }
        return json_encode(["message" => "User registered"]);
    }
    
    public function login($email, $password){
        $user = $this->userModel->show($email);
        if(!$user || !password_verify($password, $user['password'])){
            return json_encode(["message" => "Invalid email or password"]);
        }
        $payload = [
            "iss" => "localhost:8080",
            "iat" => time(),
            "exp" => time() + 3600,
            "userId" => $user['id'],
            "profile" => $user['profile']
        ];
        
        $jwt = JWT::encode($payload, getenv('JWT_SECRET_KEY'), getenv('JWT_ALGORITHM'));
        
        echo json_encode(["token" => $jwt]);
    }
}
