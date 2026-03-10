<?php

/**
 * Mapeamento explícito de rotas da aplicação.
 *
 * Formato: $router->add(MÉTODO, '/uri', [Classe::class, 'método']);
 *
 * A variável $router é criada em index.php antes de incluir este arquivo.
 */

// --- Autenticação ---
$router->add('GET',  '/login',             [\App\Controller\loginController::class, 'fillLogin']);
$router->add('POST', '/login',             [\App\Controller\loginController::class, 'login']);

// --- Cadastro de usuário (público) ---
$router->add('GET',  '/user/register',     [\App\Controller\userController::class,  'fillFields']);
$router->add('POST', '/user/register',     [\App\Controller\userController::class,  'register']);

// --- Clientes (admin) ---
$router->add('GET',  '/user/open',         [\App\Controller\userController::class,  'open']);

// --- Cidades ---
$router->add('GET',  '/city/open',         [\App\Controller\cityController::class,  'open']);
$router->add('POST', '/city/register',     [\App\Controller\cityController::class,  'register']);
$router->add('GET',  '/city/delete',       [\App\Controller\cityController::class,  'delete']);
$router->add('GET',  '/city/show',         [\App\Controller\cityController::class,  'show']);
$router->add('POST', '/city/edit',         [\App\Controller\cityController::class,  'edit']);

// --- Veículos ---
$router->add('GET',  '/vehicle/open',      [\App\Controller\vehicleController::class, 'open']);
$router->add('POST', '/vehicle/register',  [\App\Controller\vehicleController::class, 'register']);

// --- Rotas de ônibus ---
$router->add('GET',  '/route/open',        [\App\Controller\routeController::class, 'open']);
$router->add('POST', '/route/register',    [\App\Controller\routeController::class, 'register']);

// --- Passagens ---
$router->add('GET',  '/tickets/open',      [\App\Controller\ticketsController::class, 'open']);
$router->add('POST', '/tickets/register',  [\App\Controller\ticketsController::class, 'register']);
$router->add('GET',  '/tickets/show',      [\App\Controller\ticketsController::class, 'show']);
$router->add('GET',  '/tickets/all',       [\App\Controller\ticketsController::class, 'all']);

// --- Logout ---
$router->add('GET',  '/logout', function () {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    session_unset();
    session_destroy();
    header('Location: /login');
    exit();
});
