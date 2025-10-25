<?php

declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, callable $handler, array $middleware = []): void
    {
        $this->addRoute('GET', $path, $handler, $middleware);
    }

    public function post(string $path, callable $handler, array $middleware = []): void
    {
        $this->addRoute('POST', $path, $handler, $middleware);
    }

    private function addRoute(string $method, string $path, callable $handler, array $middleware): void
    {
        $path = rtrim($path, '/') ?: '/';
        $pattern = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_-]*)\}#', '(?P<$1>[^/]+)', $path);
        $pattern = '#^' . str_replace('/', '\/', $pattern) . '$#';
        $this->routes[$method][] = [
            'path' => $path,
            'pattern' => $pattern,
            'handler' => $handler,
            'middleware' => $middleware,
        ];
    }

    public function dispatch(string $method, string $uri)
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $path = rtrim($path, '/') ?: '/';

        $methodRoutes = $this->routes[$method] ?? [];
        foreach ($methodRoutes as $route) {
            if (preg_match($route['pattern'], $path, $matches)) {
                $params = [];
                foreach ($matches as $key => $value) {
                    if (!is_int($key)) {
                        $params[$key] = $value;
                    }
                }

                $handler = function () use ($route, $params) {
                    return ($route['handler'])(...array_values($params));
                };

                $pipeline = array_reduce(
                    array_reverse($route['middleware']),
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

        http_response_code(404);
        echo '404 Not Found';
        return null;
    }
}
