<?php

namespace App\Controller\Api;

use App\Model\Login;
use App\Model\User;
use App\Services\JwtService;

class AuthApiController extends ApiController
{
    public function login(): void
    {
        $body = $this->body();
        $email    = trim($body['email'] ?? '');
        $password = trim($body['password'] ?? '');

        if (!$email || !$password) {
            $this->error('E-mail e senha são obrigatórios.', 422);
            return;
        }

        $loginModel = new Login();
        $user = $loginModel->login($email, $password);

        if (!$user) {
            $this->error('Credenciais inválidas.', 401);
            return;
        }

        $token = JwtService::generate([
            'sub'     => $user['id'],
            'name'    => $user['name'],
            'email'   => $user['email'],
            'profile' => $user['profile'],
        ]);

        $this->success([
            'token' => $token,
            'user'  => [
                'id'      => $user['id'],
                'name'    => $user['name'],
                'email'   => $user['email'],
                'profile' => $user['profile'],
            ],
        ], 'Login realizado com sucesso.');
    }

    public function register(): void
    {
        $body = $this->body();
        $name     = trim($body['name'] ?? '');
        $email    = trim($body['email'] ?? '');
        $password = trim($body['password'] ?? '');

        if (!$name || !$email || !$password) {
            $this->error('Nome, e-mail e senha são obrigatórios.', 422);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('E-mail inválido.', 422);
            return;
        }

        $userModel = new User();

        if ($userModel->show($email)) {
            $this->error('E-mail já cadastrado.', 409);
            return;
        }

        $userModel->setName($name);
        $userModel->setEmail($email);
        $userModel->setPassword(password_hash($password, PASSWORD_BCRYPT));

        if (!$userModel->register()) {
            $this->error('Erro ao cadastrar usuário.', 500);
            return;
        }

        $this->success(['email' => $email, 'name' => $name], 'Usuário cadastrado com sucesso.', 201);
    }
}
