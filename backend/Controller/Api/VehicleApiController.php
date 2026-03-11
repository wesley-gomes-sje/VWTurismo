<?php

namespace App\Controller\Api;

use App\Model\Vehicle;
use OpenApi\Attributes as OA;

class VehicleApiController extends ApiController
{
    public function __construct(private ?Vehicle $vehicle = null)
    {
        $this->vehicle = $vehicle ?? new Vehicle();
    }

    #[OA\Get(
        path: '/vehicles',
        summary: 'Listar veículos',
        tags: ['Veículos'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Lista de veículos'),
            new OA\Response(response: 401, description: 'Não autorizado'),
        ]
    )]
    public function index(): void
    {
        $this->success($this->vehicle->all());
    }

    #[OA\Post(
        path: '/vehicles',
        summary: 'Cadastrar veículo (admin)',
        tags: ['Veículos'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['brand', 'model', 'plate', 'year'],
                properties: [
                    new OA\Property(property: 'brand', type: 'string', example: 'Volkswagen'),
                    new OA\Property(property: 'model', type: 'string', example: 'Comfortline'),
                    new OA\Property(property: 'plate', type: 'string', example: 'ABC1D23'),
                    new OA\Property(property: 'year',  type: 'integer', example: 2023),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Veículo cadastrado'),
            new OA\Response(response: 401, description: 'Não autorizado'),
            new OA\Response(response: 422, description: 'Campos obrigatórios ausentes'),
        ]
    )]
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
