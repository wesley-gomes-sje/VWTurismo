<?php

namespace App\Controller;

use App\Model\City;
use App\Model\Route;
use App\View\menuView;

class routeController
{
    private $routeModel;
    private $cityModel;
    private $routeView;

    public function __construct()
    {
        $this->routeModel = new Route();
        $this->cityModel  = new City();
        $this->routeView  = new menuView();
    }

    public function open($message = '', $data = [], $cities = [])
    {
        $cities = $this->cityModel->all();
        $data   = $this->routeModel->all();
        $this->routeView->registerRoute($message, $data, $cities);
    }

    public function register()
    {
        $origin      = filter_input(INPUT_POST, 'origin', FILTER_VALIDATE_INT);
        $destination = filter_input(INPUT_POST, 'destination', FILTER_VALIDATE_INT);
        $distance    = filter_input(INPUT_POST, 'distance', FILTER_VALIDATE_INT);

        if (!$origin || !$destination || !$distance) {
            return $this->open('Preencha todos os dados.');
        }

        $this->routeModel->setOrigin($origin);
        $this->routeModel->setDestination($destination);
        $this->routeModel->setDistance($distance);

        if (!$this->routeModel->register()) {
            return $this->open('Erro ao registrar rota.');
        }

        return $this->open('Rota registrada com sucesso.');
    }
}
