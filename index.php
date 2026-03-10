<?php

require_once __DIR__ . '/vendor/autoload.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use App\Controller\loginController;

$url      = isset($_GET['url']) ? $_GET['url'] : '';
$urlArray = explode('/', $url);

$controllerName       = !empty($urlArray[0]) ? $urlArray[0] . 'Controller' : 'loginController';
$metodo               = !empty($urlArray[1]) ? $urlArray[1] : 'fillLogin';
$fullyQualifiedClass  = 'App\\Controller\\' . $controllerName;

if (class_exists($fullyQualifiedClass) && method_exists($fullyQualifiedClass, $metodo)) {
    $obj = new $fullyQualifiedClass();
    $obj->$metodo();
} else {
    $controller = new loginController();
    $controller->fillLogin();
}

if ($_SERVER['REQUEST_URI'] === '/logout') {
    require_once __DIR__ . '/logout.php';
}
