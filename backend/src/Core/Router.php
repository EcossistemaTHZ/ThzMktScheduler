<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Router class
 * Cuida do roteamento das requisições
 * @package App\Core
 */
class Router
{
    private array $routes = [];

    /**
     * Adiciona uma rota GET
     * @param string $path
     * @param callable|array $handler
     */
    public function get(string $path, callable|array $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    /**
     * Adiciona uma rota POST
     * @param string $path
     * @param callable|array $handler
     */
    public function post(string $path, callable|array $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    /**
     * Adiciona uma rota PUT
     * @param string $path
     * @param callable|array $handler
     */
    public function put(string $path, callable|array $handler): void
    {
        $this->add('PUT', $path, $handler);
    }

    public function delete(string $path, callable|array $handler): void
    {
        $this->add('DELETE', $path, $handler);
    }

    /**
     * Adiciona uma rota DELETE
     * @param string $path
     * @param callable|array $handler
     */
    private function add(string $method, string $path, callable|array $handler): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler
        ];
    }

    /**
     * Dispatch a request to the appropriate handler
     * @param string $method
     * @param string $uri
     */
    public function dispatch(string $method, string $uri): void
    {
        // Otimização simples para remover a query string e o caminho base se necessário
        $parsedUrl = parse_url($uri);
        $path = $parsedUrl['path'] ?? '/';

        // Remove o prefixo se estiver rodando em um subdiretório (ajuste opcional)
        // Para o servidor CLI, assume se a raiz

        $pathMatched = false;

        foreach ($this->routes as $route) {
            // Convert route parameters {id} to regex
            $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $route['path']);
            $pattern = "#^{$pattern}$#";

            if (preg_match($pattern, $path, $matches)) {
                $pathMatched = true;

                if ($route['method'] !== $method) {
                    continue;
                }

                // Filter numeric keys
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                call_user_func($route['handler'], $params);
                return;
            }
        }

        if ($pathMatched) {
            $this->sendMethodNotAllowed();
            return;
        }

        $this->sendNotFound();
    }

    /**
     * Sends a 405 Method Not Allowed response
     * @return void
     */
    private function sendMethodNotAllowed(): void
    {
        header("HTTP/1.1 405 Method Not Allowed");
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Method not allowed']);
    }

    /**
     * Sends a 404 Not Found response
     * Envia uma resposta 404 Not Found
     * @return void
     */
    private function sendNotFound(): void
    {
        header("HTTP/1.1 404 Not Found");
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Route not found']);
    }
}
