<?php

require_once __DIR__ . '/vendor/autoload.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$router = new Router(function () {
    (new loginController())->fillLogin();
});

require_once __DIR__ . '/routes/web.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri    = $_SERVER['REQUEST_URI']    ?? '/login';

$router->dispatch($method, $uri);
