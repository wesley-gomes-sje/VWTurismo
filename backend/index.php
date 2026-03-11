<?php

require_once __DIR__ . '/vendor/autoload.php';

$uri = $_SERVER['REQUEST_URI'] ?? '/';

// Requisições da API — sem sessão, com CORS e JWT
if (str_starts_with(strtok($uri, '?'), '/api/')) {
    App\Middleware\CorsMiddleware::handle();
    header('Content-Type: application/json');

    $router = new Router(function () {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Endpoint não encontrado.']);
    });

    require_once __DIR__ . '/routes/api.php';
} else {
    // Requisições web — com sessão
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $router = new Router(function () {
        (new App\Controller\loginController())->fillLogin();
    });

    require_once __DIR__ . '/routes/web.php';
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

$router->dispatch($method, $uri);
