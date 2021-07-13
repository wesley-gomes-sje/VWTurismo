<?php
require_once './View/menuView.php';
require_once './Model/clienteModel.php';
class ClienteController{
    public function __construct()
    { }
    public function listCliente(){
        $clienteModal = new Cliente();
        $data = $clienteModal->listCliente();
        $conteudo = new menuView();
        return $conteudo->listCliente($data);
    }
}
