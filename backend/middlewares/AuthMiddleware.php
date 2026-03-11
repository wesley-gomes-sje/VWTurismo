<?php

namespace App\Middleware;

class AuthMiddleware
{
    /**
     * Verifica se há um usuário autenticado na sessão.
     * Método puro — sem side effects, totalmente testável.
     */
    public static function check(): bool
    {
        return isset($_SESSION['idUser']);
    }

    /**
     * Interrompe a requisição redirecionando para /login se não autenticado.
     *
     * @param callable|null $redirectFn  Injetável em testes para evitar header()/exit().
     */
    /**
     * Retorna true se autenticado; false (e chama redirect) se não.
     * Em produção, o redirect chama exit() — nunca retorna false.
     * Em testes, retorna false após chamar $redirectFn, permitindo asserts.
     */
    public static function handle(?callable $redirectFn = null): bool
    {
        if (!self::check()) {
            if ($redirectFn !== null) {
                $redirectFn();
            } else {
                header('Location: /login');
                exit();
            }
            return false;
        }
        return true;
    }

    /**
     * Envolve um handler com verificação de autenticação.
     * Usado em routes/web.php para proteger rotas sem repetir lógica.
     *
     * @param callable|array $handler     Handler da rota (closure ou [Classe, 'método']).
     * @param callable|null  $redirectFn  Injetável em testes.
     */
    public static function protect(callable|array $handler, ?callable $redirectFn = null): callable
    {
        return function () use ($handler, $redirectFn) {
            if (!self::handle($redirectFn)) {
                return;
            }

            if (is_array($handler)) {
                [$class, $action] = $handler;
                (new $class())->$action();
            } else {
                ($handler)();
            }
        };
    }
}
