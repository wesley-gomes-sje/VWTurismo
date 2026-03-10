<?php

/**
 * Mapeamento explícito de rotas da aplicação.
 *
 * Rotas públicas: registradas diretamente.
 * Rotas protegidas: envolvidas com AuthMiddleware::protect().
 *
 * A variável $router é criada em index.php antes de incluir este arquivo.
 */

use App\Middleware\AuthMiddleware;

$auth = fn (array $handler) => AuthMiddleware::protect($handler);

// --- Rotas públicas ---
$router->add('GET',  '/login',         [\App\Controller\loginController::class, 'fillLogin']);
$router->add('POST', '/login',         [\App\Controller\loginController::class, 'login']);
$router->add('GET',  '/user/register', [\App\Controller\userController::class,  'fillFields']);
$router->add('POST', '/user/register', [\App\Controller\userController::class,  'register']);

// --- Rotas protegidas — admin ---
$router->add('GET',  '/user/open',        $auth([\App\Controller\userController::class,    'open']));
$router->add('GET',  '/city/open',        $auth([\App\Controller\cityController::class,    'open']));
$router->add('POST', '/city/register',    $auth([\App\Controller\cityController::class,    'register']));
$router->add('GET',  '/city/delete',      $auth([\App\Controller\cityController::class,    'delete']));
$router->add('GET',  '/city/show',        $auth([\App\Controller\cityController::class,    'show']));
$router->add('POST', '/city/edit',        $auth([\App\Controller\cityController::class,    'edit']));
$router->add('GET',  '/vehicle/open',     $auth([\App\Controller\vehicleController::class, 'open']));
$router->add('POST', '/vehicle/register', $auth([\App\Controller\vehicleController::class, 'register']));
$router->add('GET',  '/route/open',       $auth([\App\Controller\routeController::class,   'open']));
$router->add('POST', '/route/register',   $auth([\App\Controller\routeController::class,   'register']));

// --- Rotas protegidas — cliente ---
$router->add('GET',  '/tickets/open',     $auth([\App\Controller\ticketsController::class, 'open']));
$router->add('POST', '/tickets/register', $auth([\App\Controller\ticketsController::class, 'register']));
$router->add('GET',  '/tickets/show',     $auth([\App\Controller\ticketsController::class, 'show']));
$router->add('GET',  '/tickets/all',      $auth([\App\Controller\ticketsController::class, 'all']));

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
