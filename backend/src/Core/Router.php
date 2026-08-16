<?php

declare(strict_types=1);

namespace App\Core;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class Router implements RequestHandlerInterface
{
    /**
     * @var list<array{method: string, path: string, handler: RequestHandlerInterface}>
     */
    private array $routes = [];

    public function __construct(private readonly ResponseFactoryInterface $responseFactory)
    {
    }

    public function get(string $path, RequestHandlerInterface $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, RequestHandlerInterface $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    public function put(string $path, RequestHandlerInterface $handler): void
    {
        $this->add('PUT', $path, $handler);
    }

    public function delete(string $path, RequestHandlerInterface $handler): void
    {
        $this->add('DELETE', $path, $handler);
    }

    private function add(string $method, string $path, RequestHandlerInterface $handler): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
        ];
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $path = $request->getUri()->getPath();
        $method = $request->getMethod();
        $pathMatched = false;

        foreach ($this->routes as $route) {
            $pattern = (string) preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $route['path']);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $path, $matches) !== 1) {
                continue;
            }

            $pathMatched = true;

            if ($route['method'] !== $method) {
                continue;
            }

            $args = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

            return $route['handler']->handle($request->withAttribute('route', $args));
        }

        return $this->jsonError(
            $pathMatched ? 405 : 404,
            $pathMatched ? 'Method not allowed' : 'Route not found',
        );
    }

    private function jsonError(int $status, string $message): ResponseInterface
    {
        $response = $this->responseFactory->createResponse($status)
            ->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode(['error' => $message], JSON_THROW_ON_ERROR));

        return $response;
    }
}
