<?php

namespace App\Controller\Api;

use App\Model\City;
use OpenApi\Attributes as OA;

class CityApiController extends ApiController
{
    public function __construct(private ?City $city = null)
    {
        $this->city = $city ?? new City();
    }

    #[OA\Get(
        path: '/cities',
        summary: 'Listar cidades ativas',
        tags: ['Cidades'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Lista de cidades'),
            new OA\Response(response: 401, description: 'Não autorizado'),
        ]
    )]
    public function index(): void
    {
        $this->success($this->city->all());
    }

    #[OA\Post(
        path: '/cities',
        summary: 'Cadastrar cidade (admin)',
        tags: ['Cidades'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name'],
                properties: [new OA\Property(property: 'name', type: 'string', example: 'São Paulo')]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Cidade cadastrada'),
            new OA\Response(response: 401, description: 'Não autorizado'),
            new OA\Response(response: 422, description: 'Campo obrigatório ausente'),
        ]
    )]
    public function store(): void
    {
        $body = $this->body();
        $name = trim($body['name'] ?? '');

        if (!$name) {
            $this->error('Nome da cidade é obrigatório.', 422);
            return;
        }

        $this->city->setName($name);

        if (!$this->city->register()) {
            $this->error('Erro ao cadastrar cidade.', 500);
            return;
        }

        $this->success(['name' => $name], 'Cidade cadastrada com sucesso.', 201);
    }

    #[OA\Put(
        path: '/cities/{id}',
        summary: 'Atualizar cidade (admin)',
        tags: ['Cidades'],
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name'],
                properties: [new OA\Property(property: 'name', type: 'string', example: 'Campinas')]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Cidade atualizada'),
            new OA\Response(response: 404, description: 'Cidade não encontrada'),
            new OA\Response(response: 422, description: 'Campo obrigatório ausente'),
        ]
    )]
    public function update(string $id): void
    {
        $body = $this->body();
        $name = trim($body['name'] ?? '');

        if (!$name) {
            $this->error('Nome da cidade é obrigatório.', 422);
            return;
        }

        if (!$this->city->show($id)) {
            $this->error('Cidade não encontrada.', 404);
            return;
        }

        if (!$this->city->edit($id, $name)) {
            $this->error('Erro ao atualizar cidade.', 500);
            return;
        }

        $this->success(['id' => $id, 'name' => $name], 'Cidade atualizada com sucesso.');
    }

    #[OA\Delete(
        path: '/cities/{id}',
        summary: 'Remover cidade (soft delete, admin)',
        tags: ['Cidades'],
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Cidade removida'),
            new OA\Response(response: 404, description: 'Cidade não encontrada'),
        ]
    )]
    public function destroy(string $id): void
    {
        if (!$this->city->show($id)) {
            $this->error('Cidade não encontrada.', 404);
            return;
        }

        if (!$this->city->delete($id)) {
            $this->error('Erro ao remover cidade.', 500);
            return;
        }

        $this->success(null, 'Cidade removida com sucesso.');
    }
}
