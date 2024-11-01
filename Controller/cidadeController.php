<?php
require_once './View/menuView.php';
require_once './Model/cityModel.php';
class cidadeController
{
    public function __construct()
    { 
      
    }
    public function Abrir($mensagem = '',$data=[])
    { 
        $cityModel = new Cidade();
        $data = $cityModel->listCidade();
        $conteudo = new menuView();
        return $conteudo->salvCidade($mensagem,$data);
    }
   
    public function salvCidade(){
        $nomeCidade = filter_input(INPUT_POST, 'nomeCidade', FILTER_SANITIZE_STRING);
        if (!$nomeCidade) {

            return $this->Abrir('Preencha todos os dados.');
        }
        $cityModel = new Cidade();
        $cityModel->setnomeCidade($nomeCidade);
        if ($cityModel->cadCidade()== true) {
            $data = $cityModel->listCidade();
           return $this->Abrir('Salvo',$data);
          }
          return $this->Abrir('erro.');

    }
    public function excluir(){
        $id= filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT);
        if(is_int($id)){
           $cityModel = new Cidade($id);
          if($cityModel->excluir($id)==true){
            return $this->Abrir('Cidade excluida.');
          };

        }
    }
    public function editar(){
        $id= filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT);
        if(is_int($id)){
           
            $cityModel = new Cidade($id);
            $dados = $cityModel->listCidadeUnica($id);

            $conteudo = new menuView();
          return $conteudo->editarCidade($dados);
        }
    }
    public function editarCidade(){
        $id= filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT);
        if(is_int($id)){
        $nomeCidade = filter_input(INPUT_POST, 'nomeCidade', FILTER_SANITIZE_STRING);
        if (!$nomeCidade) {

            return $this->Abrir('Preencha todos os dados.');
        }

        $cityModel = new Cidade();
        if($cityModel->editar($id,$nomeCidade)==true){
            return $this->Abrir('Editado.');
        };

            
        }
    }
  
}
