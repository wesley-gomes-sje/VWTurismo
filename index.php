<?php
require_once './Controller/loginController.php';
require_once './Controller/userController.php';
require_once './Controller/vehicleController.php';
require_once './Controller/cityController.php';
require_once './Controller/routeController.php';
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
    $loginController->fillLogin();
}
