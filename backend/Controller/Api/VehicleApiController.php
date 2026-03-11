<?php

namespace App\Controller\Api;

use App\Model\Vehicle;

class VehicleApiController extends ApiController
{
    public function index(): void
    {
        $vehicles = (new Vehicle())->all();
        $this->success($vehicles);
    }

    public function store(): void
    {
        $body  = $this->body();
        $brand = trim($body['brand'] ?? '');
        $model = trim($body['model'] ?? '');
        $plate = trim($body['plate'] ?? '');
        $year  = trim($body['year'] ?? '');

        if (!$brand || !$model || !$plate || !$year) {
            $this->error('Marca, modelo, placa e ano são obrigatórios.', 422);
            return;
        }

        $vehicle = new Vehicle();
        $vehicle->setBrand($brand);
        $vehicle->setModel($model);
        $vehicle->setPlate($plate);
        $vehicle->setYear($year);

        if (!$vehicle->register()) {
            $this->error('Erro ao cadastrar veículo.', 500);
            return;
        }

        $this->success(compact('brand', 'model', 'plate', 'year'), 'Veículo cadastrado com sucesso.', 201);
    }
}
