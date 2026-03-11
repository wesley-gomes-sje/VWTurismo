<?php

namespace App\Controller\Api;

use App\Middleware\JwtMiddleware;

abstract class ApiController
{
    protected function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    protected function success(mixed $data, string $message = '', int $status = 200): void
    {
        $this->json(['success' => true, 'data' => $data, 'message' => $message], $status);
    }

    protected function error(string $message, int $status = 400): void
    {
        $this->json(['success' => false, 'message' => $message], $status);
    }

    protected function body(): array
    {
        $raw = file_get_contents('php://input');
        return json_decode($raw, true) ?? [];
    }

    protected function currentUser(): array|false
    {
        return JwtMiddleware::payload();
    }

    protected function isAdmin(): bool
    {
        $user = $this->currentUser();
        return $user && ($user['profile'] ?? '') === 'admin';
    }
}
