<?php
require_once './Model/trajetoModel.php';
require_once './View/menuView.php';

class trajetoController{
    public function __construct()
    { }

    public function Abrir($mensagem = '',$dataBD = [],$dataCidades = [])
    {
        $trajetoModel = new Trajeto();
        $dataCidades = $trajetoModel->listCidOrigem();
        $dataBD = $trajetoModel->listTrajeto();
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
        
        
        $trajetoModel = new Trajeto();
        $trajetoModel->setcidadeOrigem($cidadeOrigem);
        $trajetoModel->setcidadeDestino($cidadeDestino);
        $trajetoModel->setdistancia($distancia);
        $trajetoModel->setpreco($preco);


        if($trajetoModel->cadTrajeto()==true){

            //$dataBD = $trajetoModel->listTrajeto();
            //print_r($dataBD);
            $dataCidades = $trajetoModel->listCidOrigem();
           $this->Abrir('Salvo',[],$dataCidades);
        }
        else{
           $this->Abrir('erro.');

        }
       

    }
}