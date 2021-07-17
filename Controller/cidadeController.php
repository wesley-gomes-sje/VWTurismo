<?php
require_once './View/menuView.php';
require_once './Model/cidadeModel.php';
class cidadeController
{
    public function __construct()
    { 
      
    }
    public function Abrir($mensagem = '',$data=[])
    { 
        $cidadeModel = new Cidade();
        $data = $cidadeModel->listCidade();
        $conteudo = new menuView();
        return $conteudo->salvCidade($mensagem,$data);
    }
   
    public function salvCidade(){
        $nomeCidade = filter_input(INPUT_POST, 'nomeCidade', FILTER_SANITIZE_STRING);
        if (!$nomeCidade) {

            return $this->Abrir('Preencha todos os dados.');
        }
        $cidadeModel = new Cidade();
        $cidadeModel->setnomeCidade($nomeCidade);
        if ($cidadeModel->cadCidade()== true) {
            $data = $cidadeModel->listCidade();
           return $this->Abrir('Salvo',$data);
          }
          return $this->Abrir('erro.');

    }
    public function excluir(){
        $id= filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT);
        if(is_int($id)){
           $cidadeModel = new Cidade($id);
          if($cidadeModel->excluir($id)==true){
            return $this->Abrir('Cidade excluida.');
          };

        }
    }
    public function editar(){
        $id= filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT);
        if(is_int($id)){
           
            $cidadeModel = new Cidade($id);
            $dados = $cidadeModel->listCidadeUnica($id);

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

        $cidadeModel = new Cidade();
        if($cidadeModel->editar($id,$nomeCidade)==true){
            return $this->Abrir('Editado.');
        };

            
        }
    }
  
}
