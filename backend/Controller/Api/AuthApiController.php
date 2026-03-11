<?php

namespace App\Controller\Api;

use App\Model\Login;
use App\Model\User;
use App\Services\JwtService;
use OpenApi\Attributes as OA;

class AuthApiController extends ApiController
{
    public function __construct(
        private ?Login $loginModel = null,
        private ?User  $userModel  = null
    ) {
        $this->loginModel = $loginModel ?? new Login();
        $this->userModel  = $userModel  ?? new User();
    }

    #[OA\Post(
        path: '/auth/login',
        summary: 'Autenticar usuário',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email',    type: 'string', format: 'email', example: 'admin@vwturismo.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'secret123'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Login bem-sucedido — retorna JWT',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'data', properties: [
                            new OA\Property(property: 'token', type: 'string'),
                            new OA\Property(property: 'user', properties: [
                                new OA\Property(property: 'id',      type: 'integer'),
                                new OA\Property(property: 'name',    type: 'string'),
                                new OA\Property(property: 'email',   type: 'string'),
                                new OA\Property(property: 'profile', type: 'string', enum: ['admin', 'user']),
                            ], type: 'object'),
                        ], type: 'object'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Credenciais inválidas'),
            new OA\Response(response: 422, description: 'Campos obrigatórios ausentes'),
        ]
    )]
    public function login(): void
    {
        $body     = $this->body();
        $email    = trim($body['email'] ?? '');
        $password = trim($body['password'] ?? '');

        if (!$email || !$password) {
            $this->error('E-mail e senha são obrigatórios.', 422);
            return;
        }

        $user = $this->loginModel->login($email, $password);

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

    #[OA\Post(
        path: '/auth/register',
        summary: 'Cadastrar novo cliente',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'email', 'password'],
                properties: [
                    new OA\Property(property: 'name',     type: 'string', example: 'João Silva'),
                    new OA\Property(property: 'email',    type: 'string', format: 'email'),
                    new OA\Property(property: 'password', type: 'string', format: 'password'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Usuário cadastrado com sucesso'),
            new OA\Response(response: 409, description: 'E-mail já cadastrado'),
            new OA\Response(response: 422, description: 'Campos inválidos'),
        ]
    )]
    public function register(): void
    {
        $body     = $this->body();
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

        if ($this->userModel->show($email)) {
            $this->error('E-mail já cadastrado.', 409);
            return;
        }

        $this->userModel->setName($name);
        $this->userModel->setEmail($email);
        $this->userModel->setPassword(password_hash($password, PASSWORD_BCRYPT));

        if (!$this->userModel->register()) {
            $this->error('Erro ao cadastrar usuário.', 500);
            return;
        }

        $this->success(['email' => $email, 'name' => $name], 'Usuário cadastrado com sucesso.', 201);
    }
}
