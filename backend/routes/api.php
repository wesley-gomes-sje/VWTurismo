<?php

/**
 * Rotas da API REST.
 *
 * Públicas  : sem proteção JWT.
 * Auth      : qualquer usuário autenticado (admin ou user).
 * AdminOnly : somente perfil admin.
 *
 * A variável $router é criada em index.php antes de incluir este arquivo.
 */

use App\Controller\Api\AuthApiController;
use App\Controller\Api\CityApiController;
use App\Controller\Api\RouteApiController;
use App\Controller\Api\TicketApiController;
use App\Controller\Api\UserApiController;
use App\Controller\Api\VehicleApiController;
use App\Middleware\JwtMiddleware;

$auth      = fn(array $handler) => JwtMiddleware::protect($handler);
$adminOnly = fn(array $handler) => JwtMiddleware::protect($handler, 'admin');

// --- Auth (público) ---
$router->add('POST', '/api/auth/login',    [AuthApiController::class, 'login']);
$router->add('POST', '/api/auth/register', [AuthApiController::class, 'register']);

// --- Cities ---
$router->add('GET',    '/api/cities',        $auth([CityApiController::class, 'index']));
$router->add('POST',   '/api/cities',        $adminOnly([CityApiController::class, 'store']));
$router->add('PUT',    '/api/cities/{id}',   $adminOnly([CityApiController::class, 'update']));
$router->add('DELETE', '/api/cities/{id}',   $adminOnly([CityApiController::class, 'destroy']));

// --- Vehicles ---
$router->add('GET',  '/api/vehicles',      $auth([VehicleApiController::class, 'index']));
$router->add('POST', '/api/vehicles',      $adminOnly([VehicleApiController::class, 'store']));

// --- Routes ---
$router->add('GET',  '/api/routes',        $auth([RouteApiController::class, 'index']));
$router->add('POST', '/api/routes',        $adminOnly([RouteApiController::class, 'store']));

// --- Tickets ---
$router->add('GET',  '/api/tickets',       $auth([TicketApiController::class, 'index']));
$router->add('POST', '/api/tickets',       $auth([TicketApiController::class, 'store']));
$router->add('GET',  '/api/tickets/{id}',  $auth([TicketApiController::class, 'show']));

// --- Users (admin) ---
$router->add('GET', '/api/users',          $adminOnly([UserApiController::class, 'index']));
$router->add('GET', '/api/users/{id}',     $adminOnly([UserApiController::class, 'show']));
