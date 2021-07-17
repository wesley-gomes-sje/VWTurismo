<?php

require_once './Model/passagemModel.php';
require_once './View/menuView.php';

class passagemController
{
    public function __construct()
    { }
    public function Abrir($mensagem = '', $passagemDB=[])
    {
        $passagemModel = new Passagem();
        $dataCidades = $passagemModel->listCidOrigem();
        $onibusDB = $passagemModel->listOnibus();
        $conteudo = new menuView();
        $conteudo->salvPassagem($mensagem, $dataCidades, $onibusDB,$passagemDB);
    }

    public function salvPassagem()
    {
        $idUsuario = $_SESSION['idUsuario'];
        $idOnibus = filter_input(INPUT_POST, 'onibus', FILTER_VALIDATE_INT);
        $cidadeOrigem = filter_input(INPUT_POST, 'cidadeOrigem', FILTER_VALIDATE_INT);
        $cidadeDestino = filter_input(INPUT_POST, 'cidadeDestino', FILTER_VALIDATE_INT);
        $data = filter_input(INPUT_POST, 'data', FILTER_SANITIZE_STRING);

        if (!$idUsuario || !$cidadeOrigem || !$cidadeDestino || !$data) {
            return $this->Abrir('Preencha todos os dados.');
        }
        $passagemModel = new Passagem();
        $existeTrajeto = $passagemModel->verificaTrajeto($cidadeOrigem, $cidadeDestino);

        if ($existeTrajeto == false) {
            return $this->Abrir('Trajeto não existe!');
        } else {
            $passagemModel = new Passagem();
            $passagemModel->verificaTrajeto($cidadeOrigem, $cidadeDestino);

            $passagemModel->setidUsuario($idUsuario);
            $passagemModel->setidOnibus($idOnibus);
            $idTrajeto = $_SESSION['idTrajeto'];
            $passagemModel->setidTrajeto($idTrajeto);
            $passagemModel->setdata($data);

            if ($passagemModel->cadPassagem() == true) {
                $passagemDB = $passagemModel->listPassagem($idUsuario);
                return $this->Abrir('Passagem Comprada.',$passagemDB);
            } else {
                return $this->Abrir('Erro');
            }
        }
    }

    public function listPassagem(){
        $idUsuario = $_SESSION['idUsuario'];
        $passagemModel = new Passagem();
        $dataPassagem =  $passagemModel->listPassagemTudo($idUsuario);
            $conteudo = new menuView();
            return $conteudo->listPassagem($dataPassagem);
    }
    public function listTudo(){
        $passagemModel = new Passagem();
        $data = $passagemModel->listTudo();
        $conteudo = new menuView();
        $conteudo->listTudo($data);

    }
}
