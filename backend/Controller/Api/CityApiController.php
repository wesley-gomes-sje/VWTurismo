<?php

namespace App\Controller\Api;

use App\Model\City;

class CityApiController extends ApiController
{
    public function __construct(private ?City $city = null)
    {
        $this->city = $city ?? new City();
    }

    public function index(): void
    {
        $this->success($this->city->all());
    }

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
