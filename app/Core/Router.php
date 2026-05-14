<?php

namespace App\Core;

class Router
{
    private array $routes = [];
    private array $middlewareStack = [];
    private array $groupMiddleware = [];
    private string $groupPrefix = '';

    public function get(string $uri, $action, array $middleware = []): void
    {
        $this->addRoute('GET', $uri, $action, $middleware);
    }

    public function post(string $uri, $action, array $middleware = []): void
    {
        $this->addRoute('POST', $uri, $action, $middleware);
    }

    public function put(string $uri, $action, array $middleware = []): void
    {
        $this->addRoute('PUT', $uri, $action, $middleware);
    }

    public function delete(string $uri, $action, array $middleware = []): void
    {
        $this->addRoute('DELETE', $uri, $action, $middleware);
    }

    public function group(array $attributes, callable $callback): void
    {
        $previousPrefix = $this->groupPrefix;
        $previousMiddleware = $this->groupMiddleware;

        if (isset($attributes['prefix'])) {
            $this->groupPrefix = $previousPrefix . '/' . trim($attributes['prefix'], '/');
        }
        if (isset($attributes['middleware'])) {
            $this->groupMiddleware = array_merge($previousMiddleware, (array) $attributes['middleware']);
        }

        $callback($this);

        $this->groupPrefix = $previousPrefix;
        $this->groupMiddleware = $previousMiddleware;
    }

    private function addRoute(string $method, string $uri, $action, array $middleware): void
    {
        $uri = $this->groupPrefix . '/' . trim($uri, '/');
        $uri = '/' . trim($uri, '/');

        $middleware = array_merge($this->groupMiddleware, $middleware);

        $this->routes[] = [
            'method'     => $method,
            'uri'        => $uri,
            'action'     => $action,
            'middleware' => $middleware,
            'pattern'    => $this->buildPattern($uri),
        ];
    }

    private function buildPattern(string $uri): string
    {
        $pattern = preg_replace_callback('/\{([a-zA-Z_]+)\}/', function ($match) {
            return '(?P<' . $match[1] . '>[^/]+)';
        }, $uri);
        return '#^' . $pattern . '$#';
    }

    public function dispatch(string $uri, string $method): void
    {
        // Normalize URI
        $uri = '/' . trim($uri, '/') ?: '/';

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (preg_match($route['pattern'], $uri, $matches)) {
                // Extract named params, filter out numeric keys
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                // Run middleware chain
                foreach ($route['middleware'] as $mwDef) {
                    // Parse "MiddlewareName:param" syntax
                    $mwParams = [];
                    if (str_contains($mwDef, ':')) {
                        [$mwName, $mwParam] = explode(':', $mwDef, 2);
                        $mwParams = [$mwParam];
                    } else {
                        $mwName = $mwDef;
                    }

                    $middlewareClass = "App\\Middleware\\{$mwName}";
                    if (class_exists($middlewareClass)) {
                        $middleware = new $middlewareClass();

                        // Handle RBACMiddleware parameter injection
                        if ($middleware instanceof \App\Middleware\RBACMiddleware && !empty($mwParams)) {
                            $middleware->setPermission($mwParams[0]);
                        }

                        $result = $middleware->handle($params);
                        if ($result === false) {
                            return; // Middleware blocked the request
                        }
                    }
                }

                // Resolve controller action
                if (is_string($route['action']) && str_contains($route['action'], '@')) {
                    [$controller, $method] = explode('@', $route['action'], 2);
                    $controllerClass = "App\\Controllers\\{$controller}";
                    $instance = new $controllerClass();

                    if (!method_exists($instance, $method)) {
                        Response::error(500);
                    }

                    call_user_func_array([$instance, $method], $params);
                } elseif (is_callable($route['action'])) {
                    call_user_func_array($route['action'], $params);
                } else {
                    Response::error(500);
                }

                return;
            }
        }

        Response::error(404);
    }
}