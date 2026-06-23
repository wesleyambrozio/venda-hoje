<?php

declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [];
    private array $middleware = [];

    public function get(string $path, array $handler, array $middleware = []): void
    {
        $this->addRoute('GET', $path, $handler, $middleware);
    }

    public function post(string $path, array $handler, array $middleware = []): void
    {
        $this->addRoute('POST', $path, $handler, $middleware);
    }

    private function addRoute(string $method, string $path, array $handler, array $middleware): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'middleware' => $middleware,
        ];
    }

    public function dispatch(string $uri, string $method): void
    {
        $uri = parse_url($uri, PHP_URL_PATH) ?: '/';
        $basePath = parse_url(config('app.url'), PHP_URL_PATH) ?: '';
        if ($basePath && str_starts_with($uri, $basePath)) {
            $uri = substr($uri, strlen($basePath)) ?: '/';
        }
        $uri = rtrim($uri, '/') ?: '/';

        foreach ($this->routes as $route) {
            $params = $this->match($route['path'], $uri);
            if ($params !== false && $route['method'] === $method) {
                $this->runMiddleware($route['middleware']);
                [$controllerClass, $action] = $route['handler'];
                $controller = new $controllerClass();
                call_user_func_array([$controller, $action], $params);
                return;
            }
        }

        http_response_code(404);
        View::render('pages/errors/404', ['title' => 'Página não encontrada'], 'guest');
    }

    private function match(string $routePath, string $uri): array|false
    {
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '([^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';

        if (!preg_match($pattern, $uri, $matches)) {
            return false;
        }

        array_shift($matches);
        return $matches;
    }

    private function runMiddleware(array $middleware): void
    {
        foreach ($middleware as $mw) {
            $instance = new $mw();
            $instance->handle();
        }
    }
}
