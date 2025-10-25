<?php

declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [];
    private array $middlewares = [];

    public function get(string $path, callable $handler, array $middleware = []): void
    {
        $this->routes['GET'][$path] = $handler;
        $this->middlewares['GET'][$path] = $middleware;
    }

    public function post(string $path, callable $handler, array $middleware = []): void
    {
        $this->routes['POST'][$path] = $handler;
        $this->middlewares['POST'][$path] = $middleware;
    }

    public function dispatch(string $method, string $uri)
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $path = rtrim($path, '/') ?: '/';

        $methodRoutes = $this->routes[$method] ?? [];
        $handler = $methodRoutes[$path] ?? null;

        if (!$handler) {
            http_response_code(404);
            echo '404 Not Found';
            return null;
        }

        $middlewares = $this->middlewares[$method][$path] ?? [];
        $pipeline = array_reduce(
            array_reverse($middlewares),
            function ($next, $middleware) {
                return function () use ($middleware, $next) {
                    return $middleware($next);
                };
            },
            $handler
        );

        return $pipeline();
    }
}
