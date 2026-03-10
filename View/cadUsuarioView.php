<?php

namespace App\View;

class cadUsuarioView
{
    public function formLogin(string $message): void
    {
        ob_start();
        include __DIR__ . '/Templates/partials/login.php';
        $content = ob_get_clean();
        include __DIR__ . '/Templates/templateUser.php';
    }

    public function formRegister(string $message): void
    {
        ob_start();
        include __DIR__ . '/Templates/partials/register.php';
        $content = ob_get_clean();
        include __DIR__ . '/Templates/templateUser.php';
    }
}
