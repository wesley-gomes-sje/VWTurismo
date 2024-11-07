<?php
require_once './Model/vehicleModel.php';
require_once './View/menuView.php';
class vehicleController
{
    private $vehicleModel;
    private $vehicleView;
    
    public function __construct() {
        $this->vehicleModel = new Vehicle();
        $this->vehicleView = new menuView();
    }

    public function open($message = '', $data = [])
    {
        $data = $this->vehicleModel->all();
        return $this->vehicleView->createVehicle($message, $data);
    }

    public function  createVehicle()
    {
        $brand = $this->sanitizeString($_POST['brand'] ?? '');
        $model = $this->sanitizeString($_POST['model'] ?? '');
        $plate = $this->sanitizeString($_POST['plate'] ?? '');
        $year = $this->sanitizeString($_POST['year'] ?? '');

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
        
        $data = $this->vehicleModel->all();
        return $this->open('Veículo registrado com sucesso.', $data);
    }
    
    private function sanitizeString(?string $string): string
    {
        return htmlspecialchars(strip_tags($string ?? ''), ENT_QUOTES, 'UTF-8');
    }
}
