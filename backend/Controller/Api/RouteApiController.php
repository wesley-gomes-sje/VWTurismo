<?php

namespace App\Controller\Api;

use App\Model\Route;
use OpenApi\Attributes as OA;

class RouteApiController extends ApiController
{
    public function __construct(private ?Route $route = null)
    {
        $this->route = $route ?? new Route();
    }

    #[OA\Get(
        path: '/routes',
        summary: 'Listar rotas',
        tags: ['Rotas'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Lista de rotas'),
            new OA\Response(response: 401, description: 'Não autorizado'),
        ]
    )]
    public function index(): void
    {
        $this->success($this->route->all() ?: []);
    }

    #[OA\Post(
        path: '/routes',
        summary: 'Cadastrar rota (admin)',
        tags: ['Rotas'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['origin', 'destination', 'distance'],
                properties: [
                    new OA\Property(property: 'origin',      type: 'integer', description: 'ID da cidade de origem',  example: 1),
                    new OA\Property(property: 'destination', type: 'integer', description: 'ID da cidade de destino', example: 2),
                    new OA\Property(property: 'distance',    type: 'number',  description: 'Distância em km',         example: 430.5),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Rota cadastrada'),
            new OA\Response(response: 401, description: 'Não autorizado'),
            new OA\Response(response: 422, description: 'Campos obrigatórios ausentes'),
        ]
    )]
    public function store(): void
    {
        $body        = $this->body();
        $origin      = trim($body['origin'] ?? '');
        $destination = trim($body['destination'] ?? '');
        $distance    = $body['distance'] ?? null;

        if (!$origin || !$destination || $distance === null) {
            $this->error('Origem, destino e distância são obrigatórios.', 422);
            return;
        }

        $this->route->setOrigin($origin);
        $this->route->setDestination($destination);
        $this->route->setDistance((float) $distance);

        if (!$this->route->register()) {
            $this->error('Erro ao cadastrar rota.', 500);
            return;
        }

        $this->success(compact('origin', 'destination', 'distance'), 'Rota cadastrada com sucesso.', 201);
    }
}
