<?php
require_once './Model/onibusModel.php';
require_once './View/menuView.php';
class onibusController
{
    public function __construct()
    { }
    public function Abrir($mensagem = '',$data=[])
    {
        $onibusModel = new Onibus();
        $data = $onibusModel->listOnibus();
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
        $onibusModel = new Onibus();
        $onibusModel->setnomeOnibus($nomeOnibus);
        $onibusModel->setplaca($placa);
        if ($onibusModel->cadOnibus() == true) {
          $data = $onibusModel->listOnibus();
         return $this->Abrir('Salvo',$data);
        }
        return $this->Abrir('erro.');
    }
}
