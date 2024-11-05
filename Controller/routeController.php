<?php
require_once './Model/routeModel.php';
require_once './Model/cityModel.php';
require_once './View/menuView.php';

class routeController
{
    private $routeModel;
    private $cityModel;
    private $routeView;

    public function __construct()
    {
        $this->routeModel = new Route();
        $this->cityModel = new City();
        $this->routeView = new menuView();
    }

    public function Open($message = '', $data = [], $cities = [])
    {
        $cities = $this->cityModel->all();
        $data = $this->routeModel->all();
        $conteudo = $this->routeView;

        $conteudo->registerRoute($message, $data, $cities);
    }

    public function register()
    {
        $origin = filter_input(INPUT_POST, 'origin', FILTER_VALIDATE_INT);
        $destination = filter_input(INPUT_POST, 'destination', FILTER_VALIDATE_INT);
        $distance = filter_input(INPUT_POST, 'distance', FILTER_VALIDATE_INT);

        if (!$origin || !$destination || !$distance) {
            return $this->Open('Preencha todos os dados.');
        }

        $this->routeModel->setOrigin($origin);
        $this->routeModel->setDestination($destination);
        $this->routeModel->setDistance($distance);


        if (!$this->routeModel->register()) {
            return $this->Open('Erro ao registrar rota.');
        }

        $cities = $this->cityModel->all();
        $this->Open('Rota registrada com sucesso.', [], $cities);
    }
}
