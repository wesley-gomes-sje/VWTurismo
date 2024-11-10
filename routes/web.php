<?php
require_once '../config/config.php';

use Controllers\AuthController;

$auth = new AuthController($pdo);

$url = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {

    if ($url === '/login') {
        $auth->login($_POST['email'], $_POST['password']);
        return;
    }

    if ($url === '/register') {
        $auth->register($_POST['name'], $_POST['email'], $_POST['password'], $_POST['confirm_password']);
        return;
    }

    return;
}

if ($method === 'GET') {
    if ($url === '/login') {
        include '../Views/auth/login.php';
        return;
    }
}
