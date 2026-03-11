<?php

namespace App\Controller\Api;

use App\Model\User;
use OpenApi\Attributes as OA;

class UserApiController extends ApiController
{
    public function __construct(private ?User $user = null)
    {
        $this->user = $user ?? new User();
    }

    #[OA\Get(
        path: '/users',
        summary: 'Listar clientes (admin)',
        tags: ['Usuários'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Lista de clientes'),
            new OA\Response(response: 401, description: 'Não autorizado'),
        ]
    )]
    public function index(): void
    {
        $this->success($this->user->showCustomers());
    }

    #[OA\Get(
        path: '/users/{id}',
        summary: 'Detalhe de um usuário (admin)',
        tags: ['Usuários'],
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Dados do usuário'),
            new OA\Response(response: 404, description: 'Usuário não encontrado'),
        ]
    )]
    public function show(string $id): void
    {
        $all   = $this->user->all();
        $found = array_values(array_filter($all, fn($u) => ($u['id'] ?? null) == $id));

        if (!$found) {
            $this->error('Usuário não encontrado.', 404);
            return;
        }

        $this->success($found[0]);
    }
}
