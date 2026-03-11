<?php

namespace App\Controller\Api;

use App\Model\Vehicle;

class VehicleApiController extends ApiController
{
    public function __construct(private ?Vehicle $vehicle = null)
    {
        $this->vehicle = $vehicle ?? new Vehicle();
    }

    public function index(): void
    {
        $this->success($this->vehicle->all());
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

        $this->vehicle->setBrand($brand);
        $this->vehicle->setModel($model);
        $this->vehicle->setPlate($plate);
        $this->vehicle->setYear($year);

        if (!$this->vehicle->register()) {
            $this->error('Erro ao cadastrar veículo.', 500);
            return;
        }

        $this->success(compact('brand', 'model', 'plate', 'year'), 'Veículo cadastrado com sucesso.', 201);
    }
}
