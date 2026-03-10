<?php

/**
 * Mapeamento explícito de rotas da aplicação.
 *
 * Rotas públicas: registradas diretamente.
 * Rotas protegidas: envolvidas com AuthMiddleware::protect().
 *
 * A variável $router é criada em index.php antes de incluir este arquivo.
 */

$auth = fn (array $handler) => AuthMiddleware::protect($handler);

// --- Rotas públicas ---
$router->add('GET',  '/login',         ['loginController', 'fillLogin']);
$router->add('POST', '/login',         ['loginController', 'login']);
$router->add('GET',  '/user/register', ['userController',  'fillFields']);
$router->add('POST', '/user/register', ['userController',  'register']);

// --- Rotas protegidas — admin ---
$router->add('GET',  '/user/open',        $auth(['userController',   'open']));
$router->add('GET',  '/city/open',        $auth(['cityController',   'open']));
$router->add('POST', '/city/register',    $auth(['cityController',   'register']));
$router->add('GET',  '/city/delete',      $auth(['cityController',   'delete']));
$router->add('GET',  '/city/show',        $auth(['cityController',   'show']));
$router->add('POST', '/city/edit',        $auth(['cityController',   'edit']));
$router->add('GET',  '/vehicle/open',     $auth(['vehicleController','open']));
$router->add('POST', '/vehicle/register', $auth(['vehicleController','register']));
$router->add('GET',  '/route/open',       $auth(['routeController',  'open']));
$router->add('POST', '/route/register',   $auth(['routeController',  'register']));

// --- Rotas protegidas — cliente ---
$router->add('GET',  '/tickets/open',     $auth(['ticketsController','open']));
$router->add('POST', '/tickets/register', $auth(['ticketsController','register']));
$router->add('GET',  '/tickets/show',     $auth(['ticketsController','show']));
$router->add('GET',  '/tickets/all',      $auth(['ticketsController','all']));

// --- Logout ---
$router->add('GET', '/logout', function () {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    session_unset();
    session_destroy();
    header('Location: /login');
    exit();
});
