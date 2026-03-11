<?php

namespace App\Controller\Api;

use App\Model\Route;
use App\Model\Ticket;

class TicketApiController extends ApiController
{
    public function __construct(
        private ?Ticket $ticket = null,
        private ?Route  $route  = null
    ) {
        $this->ticket = $ticket ?? new Ticket();
        $this->route  = $route  ?? new Route();
    }

    public function index(): void
    {
        $user = $this->currentUser();

        $data = ($user['profile'] === 'admin')
            ? $this->ticket->all()
            : $this->ticket->showTicketsByPassenger($user['sub']);

        $this->success($data ?: []);
    }

    public function show(string $id): void
    {
        $ticket = $this->ticket->show($id);

        if (!$ticket) {
            $this->error('Passagem não encontrada.', 404);
            return;
        }

        $this->success($ticket);
    }

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
