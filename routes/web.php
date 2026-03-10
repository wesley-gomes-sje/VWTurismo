<?php

/**
 * Mapeamento explícito de rotas da aplicação.
 *
 * Formato: $router->add(MÉTODO, '/uri', [Classe::class, 'método']);
 *
 * A variável $router é criada em index.php antes de incluir este arquivo.
 */

// --- Autenticação ---
$router->add('GET',  '/login',             ['loginController', 'fillLogin']);
$router->add('POST', '/login',             ['loginController', 'login']);

// --- Cadastro de usuário (público) ---
$router->add('GET',  '/user/register',     ['userController',  'fillFields']);
$router->add('POST', '/user/register',     ['userController',  'register']);

// --- Clientes (admin) ---
$router->add('GET',  '/user/open',         ['userController',  'open']);

// --- Cidades ---
$router->add('GET',  '/city/open',         ['cityController',  'open']);
$router->add('POST', '/city/register',     ['cityController',  'register']);
$router->add('GET',  '/city/delete',       ['cityController',  'delete']);
$router->add('GET',  '/city/show',         ['cityController',  'show']);
$router->add('POST', '/city/edit',         ['cityController',  'edit']);

// --- Veículos ---
$router->add('GET',  '/vehicle/open',      ['vehicleController', 'open']);
$router->add('POST', '/vehicle/register',  ['vehicleController', 'register']);

// --- Rotas de ônibus ---
$router->add('GET',  '/route/open',        ['routeController', 'open']);
$router->add('POST', '/route/register',    ['routeController', 'register']);

// --- Passagens ---
$router->add('GET',  '/tickets/open',      ['ticketsController', 'open']);
$router->add('POST', '/tickets/register',  ['ticketsController', 'register']);
$router->add('GET',  '/tickets/show',      ['ticketsController', 'show']);
$router->add('GET',  '/tickets/all',       ['ticketsController', 'all']);

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
