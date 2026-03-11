<?php

namespace App\Controller;

use App\Model\Vehicle;
use App\View\menuView;

class vehicleController
{
    use SanitizeTrait;

    private $vehicleModel;
    private $vehicleView;

    public function __construct()
    {
        $this->vehicleModel = new Vehicle();
        $this->vehicleView  = new menuView();
    }

    public function open($message = '', $data = [])
    {
        $data = $this->vehicleModel->all();
        return $this->vehicleView->createVehicle($message, $data);
    }

    public function register()
    {
        $brand = $this->sanitizeString($_POST['brand'] ?? '');
        $model = $this->sanitizeString($_POST['model'] ?? '');
        $plate = $this->sanitizeString($_POST['plate'] ?? '');
        $year  = $this->sanitizeString($_POST['year'] ?? '');

        if (!$brand || !$model || !$plate || !$year) {
            $this->open('Preencha todos os dados.');
            return;
        }

        $this->vehicleModel->setBrand($brand);
        $this->vehicleModel->setModel($model);
        $this->vehicleModel->setPlate($plate);
        $this->vehicleModel->setYear($year);

        if (!$this->vehicleModel->register()) {
            $this->open('Erro ao registrar veículo');
            return;
        }

        return $this->open('Veículo registrado com sucesso.');
    }
}
