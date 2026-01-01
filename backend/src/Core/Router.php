<?php

declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, callable|array $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, callable|array $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    public function put(string $path, callable|array $handler): void
    {
        $this->add('PUT', $path, $handler);
    }

    public function delete(string $path, callable|array $handler): void
    {
        $this->add('DELETE', $path, $handler);
    }

    private function add(string $method, string $path, callable|array $handler): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler
        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        // Simple optimization to strip query string and base path if needed
        $parsedUrl = parse_url($uri);
        $path = $parsedUrl['path'] ?? '/';

        // Remove prefix if running in subdir (optional adjustment)
        // For CLI server assume root

        foreach ($this->routes as $route) {
            // Convert route parameters {id} to regex
            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $route['path']);
            $pattern = "#^{$pattern}$#";

            if ($route['method'] === $method && preg_match($pattern, $path, $matches)) {

                // Filter numeric keys
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                call_user_func($route['handler'], $params);
                return;
            }
        }

        $this->sendNotFound();
    }

    private function sendNotFound(): void
    {
        header("HTTP/1.1 404 Not Found");
        echo json_encode(['error' => 'Route not found']);
    }
}
