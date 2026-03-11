<?php

namespace App\Controller\Api;

use App\Model\Route;
use App\Model\Ticket;
use OpenApi\Attributes as OA;

class TicketApiController extends ApiController
{
    public function __construct(
        private ?Ticket $ticket = null,
        private ?Route  $route  = null
    ) {
        $this->ticket = $ticket ?? new Ticket();
        $this->route  = $route  ?? new Route();
    }

    #[OA\Get(
        path: '/tickets',
        summary: 'Listar passagens (admin: todas; user: próprias)',
        tags: ['Passagens'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Lista de passagens'),
            new OA\Response(response: 401, description: 'Não autorizado'),
        ]
    )]
    public function index(): void
    {
        $user = $this->currentUser();

        $data = ($user['profile'] === 'admin')
            ? $this->ticket->all()
            : $this->ticket->showTicketsByPassenger($user['sub']);

        $this->success($data ?: []);
    }

    #[OA\Get(
        path: '/tickets/{id}',
        summary: 'Detalhe de uma passagem',
        tags: ['Passagens'],
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Dados da passagem'),
            new OA\Response(response: 404, description: 'Passagem não encontrada'),
        ]
    )]
    public function show(string $id): void
    {
        $ticket = $this->ticket->show($id);

        if (!$ticket) {
            $this->error('Passagem não encontrada.', 404);
            return;
        }

        $this->success($ticket);
    }

    #[OA\Post(
        path: '/tickets',
        summary: 'Comprar passagem',
        tags: ['Passagens'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['origin', 'destination', 'vehicle_id', 'date'],
                properties: [
                    new OA\Property(property: 'origin',      type: 'integer', description: 'ID da cidade de origem',  example: 1),
                    new OA\Property(property: 'destination', type: 'integer', description: 'ID da cidade de destino', example: 2),
                    new OA\Property(property: 'vehicle_id',  type: 'integer', description: 'ID do veículo',           example: 3),
                    new OA\Property(property: 'date',        type: 'string',  format: 'date',                         example: '2025-06-15'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Passagem registrada'),
            new OA\Response(response: 404, description: 'Rota não encontrada'),
            new OA\Response(response: 422, description: 'Campos obrigatórios ausentes'),
        ]
    )]
    public function store(): void
    {
        $body        = $this->body();
        $origin      = trim($body['origin'] ?? '');
        $destination = trim($body['destination'] ?? '');
        $vehicleId   = $body['vehicle_id'] ?? null;
        $date        = trim($body['date'] ?? '');

        if (!$origin || !$destination || !$vehicleId || !$date) {
            $this->error('Origem, destino, veículo e data são obrigatórios.', 422);
            return;
        }

        $route = $this->route->check($origin, $destination);

        if (!$route) {
            $this->error('Rota não encontrada para origem e destino informados.', 404);
            return;
        }

        $user = $this->currentUser();
        $this->ticket->setPassenger($user['sub']);
        $this->ticket->setRoute($route['id']);
        $this->ticket->setVehicle($vehicleId);
        $this->ticket->setPrice($this->ticket->calculatePrice((float) $route['distance']));
        $this->ticket->setDate($date);

        if (!$this->ticket->register()) {
            $this->error('Erro ao registrar passagem.', 500);
            return;
        }

        $this->success([
            'route' => $route['id'],
            'price' => $this->ticket->getPrice(),
            'date'  => $date,
        ], 'Passagem registrada com sucesso.', 201);
    }
}
