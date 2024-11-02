<?php
require_once './Controller/loginController.php';
require_once './Controller/userController.php';
require_once './Controller/onibusController.php';
require_once './Controller/cidadeController.php';
require_once './Controller/trajetoController.php';
require_once './Controller/passagemController.php';
require_once './View/menuView.php';


if(isset($_GET['modulo']) && isset($_GET['metodo'])){
    $classe = $_GET['modulo'];
    $metodo = $_GET['metodo'];
    $obj = new $classe();
    $obj->$metodo();
} else if(isset($_POST['modulo']) && isset($_POST['metodo'])){
    $classe = $_POST['modulo'];
    $metodo = $_POST['metodo'];
    $obj = new $classe();
    $obj->$metodo();

}
 else{
   
    $loginController = new loginController();
    $loginController->preencherLogin();
}
