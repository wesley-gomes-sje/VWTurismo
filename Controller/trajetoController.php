<?php
require_once './Model/routeModel.php';
require_once './View/menuView.php';

class trajetoController{
    public function __construct()
    { }

    public function Abrir($mensagem = '',$dataBD = [],$dataCidades = [])
    {
        $routeModel = new Trajeto();
        $dataCidades = $routeModel->listCidOrigem();
        $dataBD = $routeModel->listTrajeto();
        //print_r($dataBD);
        $conteudo = new menuView();
        $conteudo->salvTrajeto($mensagem,$dataBD,$dataCidades);
    }

    public function salvTrajeto(){
        $cidadeOrigem = filter_input(INPUT_POST,'cidadeOrigem',FILTER_VALIDATE_INT);
        $cidadeDestino = filter_input(INPUT_POST,'cidadeDestino',FILTER_VALIDATE_INT);
        $distancia = filter_input(INPUT_POST,'distancia',FILTER_VALIDATE_INT);
        $preco = filter_input(INPUT_POST,'preco',FILTER_SANITIZE_STRING);

        if(!$cidadeOrigem || !$cidadeDestino || !$distancia ||!$preco){
            return $this->Abrir('Preencha todos os dados.');
        }
        
        
        $routeModel = new Trajeto();
        $routeModel->setcidadeOrigem($cidadeOrigem);
        $routeModel->setcidadeDestino($cidadeDestino);
        $routeModel->setdistancia($distancia);
        $routeModel->setpreco($preco);


        if($routeModel->cadTrajeto()==true){

            //$dataBD = $routeModel->listTrajeto();
            //print_r($dataBD);
            $dataCidades = $routeModel->listCidOrigem();
           $this->Abrir('Salvo',[],$dataCidades);
        }
        else{
           $this->Abrir('erro.');

        }
       

    }
}