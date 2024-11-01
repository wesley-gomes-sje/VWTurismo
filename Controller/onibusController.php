<?php
require_once './Model/vehicleModel.php';
require_once './View/menuView.php';
class onibusController
{
    public function __construct()
    { }
    public function Abrir($mensagem = '',$data=[])
    {
        $vehicleModel = new Onibus();
        $data = $vehicleModel->listOnibus();
        $conteudo = new menuView();
        return $conteudo->salvOnibus($mensagem,$data);
    }
    public function  salvOnibus()
    {
        $nomeOnibus = filter_input(INPUT_POST, 'nomeOnibus', FILTER_SANITIZE_STRING);
        $placa = filter_input(INPUT_POST, 'placa', FILTER_SANITIZE_STRING);

        if (!$nomeOnibus || !$placa) {

            return $this->Abrir('Preencha todos os dados.');
        }
        $vehicleModel = new Onibus();
        $vehicleModel->setnomeOnibus($nomeOnibus);
        $vehicleModel->setplaca($placa);
        if ($vehicleModel->cadOnibus() == true) {
          $data = $vehicleModel->listOnibus();
         return $this->Abrir('Salvo',$data);
        }
        return $this->Abrir('erro.');
    }
}
