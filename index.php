<?php
require_once './Controller/loginController.php';
require_once './Controller/userController.php';
require_once './Controller/vehicleController.php';
require_once './Controller/cityController.php';
require_once './Controller/routeController.php';
require_once './Controller/ticketsController.php';
require_once './View/menuView.php';


$url = isset($_GET['url']) ? $_GET['url'] : '';

$urlArray = explode('/', $url);
$classe = !empty($urlArray[0]) ? $urlArray[0] . 'Controller' : 'loginController';
$metodo = !empty($urlArray[1]) ? $urlArray[1] : 'fillLogin';

if (class_exists($classe) && method_exists($classe, $metodo)) {
    $obj = new $classe();
    $obj->$metodo();
    
}  else {
    $loginController = new loginController();
    $loginController->fillLogin();
}

if ($_SERVER['REQUEST_URI'] == '/logout') {
    require_once './logout.php';
}