<?php

namespace App\Controller\Api;

use App\Model\Route;

class RouteApiController extends ApiController
{
    public function __construct(private ?Route $route = null)
    {
        $this->route = $route ?? new Route();
    }

    public function index(): void
    {
        $this->success($this->route->all() ?: []);
    }

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
