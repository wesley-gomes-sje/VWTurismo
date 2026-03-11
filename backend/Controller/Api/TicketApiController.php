<?php

namespace App\Controller\Api;

use App\Model\Route;
use App\Model\Ticket;

class TicketApiController extends ApiController
{
    public function index(): void
    {
        $ticket = new Ticket();
        $user   = $this->currentUser();

        $data = ($user['profile'] === 'admin')
            ? $ticket->all()
            : $ticket->showTicketsByPassenger($user['sub']);

        $this->success($data ?: []);
    }

    public function show(string $id): void
    {
        $ticket = (new Ticket())->show($id);

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

        $route = (new Route())->check($origin, $destination);

        if (!$route) {
            $this->error('Rota não encontrada para origem e destino informados.', 404);
            return;
        }

        $user   = $this->currentUser();
        $ticket = new Ticket();
        $ticket->setPassenger($user['sub']);
        $ticket->setRoute($route['id']);
        $ticket->setVehicle($vehicleId);
        $ticket->setPrice($ticket->calculatePrice((float) $route['distance']));
        $ticket->setDate($date);

        if (!$ticket->register()) {
            $this->error('Erro ao registrar passagem.', 500);
            return;
        }

        $this->success([
            'route'    => $route['id'],
            'price'    => $ticket->getPrice(),
            'date'     => $date,
        ], 'Passagem registrada com sucesso.', 201);
    }
}
