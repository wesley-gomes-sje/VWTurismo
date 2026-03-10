<?php

class Router
{
    private array $routes = [];
    private $notFoundHandler;

    public function __construct(?callable $notFoundHandler = null)
    {
        $this->notFoundHandler = $notFoundHandler ?? static function () {
            http_response_code(404);
        };
    }

    public function add(string $method, string $uri, callable|array $handler): void
    {
        $this->routes[] = [
            'method'  => strtoupper($method),
            'uri'     => $uri,
            'handler' => $handler,
        ];
    }

    /**
     * Despacha a requisição para o handler registrado.
     *
     * @return bool true se uma rota foi encontrada, false se não.
     */
    public function dispatch(string $method, string $uri): bool
    {
        $path   = strtok($uri, '?');
        $method = strtoupper($method);

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $route['uri'] === $path) {
                $this->call($route['handler']);
                return true;
            }
        }

        ($this->notFoundHandler)();
        return false;
    }

    private function call(callable|array $handler): void
    {
        if (is_array($handler)) {
            [$class, $action] = $handler;
            (new $class())->$action();
        } else {
            ($handler)();
        }
    }
}
