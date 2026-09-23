<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, string $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, string $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    private function normalize(string $path): string
    {
        if ($path === '') {
            return '/';
        }
        $path = rtrim($path, '/');
        return $path === '' ? '/' : $path;
    }

    private function add(string $method, string $path, string $handler): void
    {
        $path = $this->normalize($path);
        preg_match_all('#\{([a-zA-Z_]+)\}#', $path, $paramNames);
        $regex = preg_replace('#\{[a-zA-Z_]+\}#', '([^/]+)', $path);
        $regex = '#^' . $regex . '$#';

        $this->routes[] = [
            'method' => $method,
            'regex' => $regex,
            'handler' => $handler,
            'params' => $paramNames[1],
        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        $uri = $this->normalize($uri);

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            if (preg_match($route['regex'], $uri, $matches)) {
                array_shift($matches);
                $args = array_combine($route['params'], array_map('urldecode', $matches));

                [$controllerName, $action] = explode('@', $route['handler']);
                $class = 'App\\Controllers\\' . $controllerName;

                if (!class_exists($class) || !method_exists($class, $action)) {
                    http_response_code(500);
                    die("Route handler not found: {$class}@{$action}");
                }

                $controller = new $class();
                call_user_func_array([$controller, $action], $args);
                return;
            }
        }

        http_response_code(404);
        (new \App\Controllers\ErrorController())->notFound();
    }
}
