<?php

namespace App\Controller\Api;

use App\Model\Route;

class RouteApiController extends ApiController
{
    public function index(): void
    {
        $routes = (new Route())->all();
        $this->success($routes ?: []);
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

        $route = new Route();
        $route->setOrigin($origin);
        $route->setDestination($destination);
        $route->setDistance((float) $distance);

        if (!$route->register()) {
            $this->error('Erro ao cadastrar rota.', 500);
            return;
        }

        $this->success(compact('origin', 'destination', 'distance'), 'Rota cadastrada com sucesso.', 201);
    }
}
