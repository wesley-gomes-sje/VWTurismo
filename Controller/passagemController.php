<?php

require_once './Model/ticketModel.php';
require_once './View/menuView.php';

class passagemController
{
    public function __construct()
    { }
    public function Abrir($mensagem = '', $passagemDB=[])
    {
        $ticketModel = new Passagem();
        $dataCidades = $ticketModel->listCidOrigem();
        $onibusDB = $ticketModel->listOnibus();
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
        $ticketModel = new Passagem();
        $existeTrajeto = $ticketModel->verificaTrajeto($cidadeOrigem, $cidadeDestino);

        if ($existeTrajeto == false) {
            return $this->Abrir('Trajeto não existe!');
        } else {
            $ticketModel = new Passagem();
            $ticketModel->verificaTrajeto($cidadeOrigem, $cidadeDestino);

            $ticketModel->setidUsuario($idUsuario);
            $ticketModel->setidOnibus($idOnibus);
            $idTrajeto = $_SESSION['idTrajeto'];
            $ticketModel->setidTrajeto($idTrajeto);
            $ticketModel->setdata($data);

            if ($ticketModel->cadPassagem() == true) {
                $passagemDB = $ticketModel->listPassagem($idUsuario);
                return $this->Abrir('Passagem Comprada.',$passagemDB);
            } else {
                return $this->Abrir('Erro');
            }
        }
    }

    public function listPassagem(){
        $idUsuario = $_SESSION['idUsuario'];
        $ticketModel = new Passagem();
        $dataPassagem =  $ticketModel->listPassagemTudo($idUsuario);
            $conteudo = new menuView();
            return $conteudo->listPassagem($dataPassagem);
    }
    public function listTudo(){
        $ticketModel = new Passagem();
        $data = $ticketModel->listTudo();
        $conteudo = new menuView();
        $conteudo->listTudo($data);

    }
}
