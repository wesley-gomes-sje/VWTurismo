<?php
require_once './Model/passagemModel.php';
require_once './View/menuView.php';

class passagemController{
    public function __construct()
    { }
    public function Abrir()
    {
        echo $_SESSION['idUsuario'];
    }
    
}