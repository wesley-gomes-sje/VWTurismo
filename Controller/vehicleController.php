<?php
require_once './Model/vehicleModel.php';
require_once './View/menuView.php';
class vehicleController
{
    public function __construct() {}

    public function Open($message = '', $data = [])
    {
        $vehicleModel = new Vehicle();
        $data = $vehicleModel->all();
        $vehicleView = new menuView();
        return $vehicleView->createVehicle($message, $data);
    }

    public function  createVehicle()
    {
        $brand = $this->sanitizeString($_POST['brand'] ?? '');
        $model = $this->sanitizeString($_POST['model'] ?? '');
        $plate = $this->sanitizeString($_POST['plate'] ?? '');
        $year = $this->sanitizeString($_POST['year'] ?? '');

        if (!$brand || !$model || !$plate || !$year) {

            $this->Open('Preencha todos os dados.');
            return;
        }
        
        $vehicleModel = new Vehicle();
        $vehicleModel->setBrand($brand);
        $vehicleModel->setModel($model);
        $vehicleModel->setPlate($plate);
        $vehicleModel->setYear($year);
        
        if (!$vehicleModel->register()) {
             $this->Open('Erro ao registrar veículo');
             return;
        }
        
        $data = $vehicleModel->all();
        return $this->Open('Veículo registrado com sucesso.', $data);
    }
    
    private function sanitizeString(?string $string): string
    {
        return htmlspecialchars(strip_tags($string ?? ''), ENT_QUOTES, 'UTF-8');
    }
}
