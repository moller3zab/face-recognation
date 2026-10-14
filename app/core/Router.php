<?php

declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, callable|array $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, callable|array $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    private function addRoute(string $method, string $path, callable|array $handler): void
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler,
        ];
    }

    public function dispatch(string $uri, string $method): void
    {
        $method = strtoupper($method);

        // Normalize base URL prefix if present
        $uri = parse_url($uri, PHP_URL_PATH) ?? '/';
        $uri = urldecode($uri);
        
        $basePaths = defined('BASE_URL') ? [rtrim(BASE_URL, '/')] : [];
        if (defined('BASE_URL') && str_ends_with(rtrim(BASE_URL, '/'), '/public')) {
            $basePaths[] = substr(rtrim(BASE_URL, '/'), 0, -strlen('/public'));
        }

        foreach (array_unique($basePaths) as $basePath) {
            if ($basePath !== '' && $basePath !== '/' && ($uri === $basePath || str_starts_with($uri, $basePath . '/'))) {
                $uri = substr($uri, strlen($basePath));
                break;
            }
        }

        $uri = rtrim($uri, '/');
        if ($uri === '') {
            $uri = '/';
        }

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $route['path'] === $uri) {
                $handler = $route['handler'];

                if (is_callable($handler)) {
                    call_user_func($handler);
                    return;
                }

                if (is_array($handler)) {
                    [$controllerClass, $action] = $handler;
                    if (class_exists($controllerClass)) {
                        $controller = new $controllerClass();
                        if (method_exists($controller, $action)) {
                            $controller->$action();
                            return;
                        }
                    }
                }
            }
        }

        // 404 Not Found fallback
        http_response_code(404);
        $errorView = APP_PATH . DS . 'views' . DS . 'errors' . DS . '404.php';
        if (file_exists($errorView)) {
            require $errorView;
        } else {
            echo '<h1>404 Page Not Found</h1>';
        }
    }
}
