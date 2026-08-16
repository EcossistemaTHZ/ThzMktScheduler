<?php

declare(strict_types=1);

namespace App\Tests\Core;

use App\Core\Router;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class RouterTest extends TestCase
{
    private Psr17Factory $factory;
    private Router $router;

    protected function setUp(): void
    {
        $this->factory = new Psr17Factory();
        $this->router = new Router($this->factory);
    }

    public function testMatchesRouteAndPassesParams(): void
    {
        $this->router->get('/api/v1/campaigns/{id}', $this->routeAwareHandler());

        $response = $this->router->handle($this->factory->createServerRequest('GET', '/api/v1/campaigns/42'));

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('42', (string) $response->getBody());
    }

    public function testDispatchByMethod(): void
    {
        $this->router->get('/api/v1/campaigns', $this->handlerWithBody('get'));
        $this->router->post('/api/v1/campaigns', $this->handlerWithBody('post'));

        self::assertSame('get', (string) $this->router->handle(
            $this->factory->createServerRequest('GET', '/api/v1/campaigns'),
        )->getBody());

        self::assertSame('post', (string) $this->router->handle(
            $this->factory->createServerRequest('POST', '/api/v1/campaigns'),
        )->getBody());
    }

    public function testMethodNotAllowed(): void
    {
        $this->router->get('/api/v1/campaigns', $this->handlerWithBody('get'));

        $response = $this->router->handle($this->factory->createServerRequest('DELETE', '/api/v1/campaigns'));

        self::assertSame(405, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        self::assertSame('Method not allowed', $body['error']);
    }

    public function testRouteNotFound(): void
    {
        $response = $this->router->handle($this->factory->createServerRequest('GET', '/api/v1/desconhecido'));

        self::assertSame(404, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        self::assertSame('Route not found', $body['error']);
    }

    private function handlerWithBody(string $content): RequestHandlerInterface
    {
        return new class ($this->factory, $content) implements RequestHandlerInterface {
            public function __construct(
                private readonly Psr17Factory $factory,
                private readonly string $content,
            ) {
            }

            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $response = $this->factory->createResponse(200);
                $response->getBody()->write($this->content);

                return $response;
            }
        };
    }

    private function routeAwareHandler(): RequestHandlerInterface
    {
        return new class ($this->factory) implements RequestHandlerInterface {
            public function __construct(private readonly Psr17Factory $factory)
            {
            }

            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $route = $request->getAttribute('route');
                $id = is_array($route) ? (string) ($route['id'] ?? 'none') : 'none';

                $response = $this->factory->createResponse(200);
                $response->getBody()->write($id);

                return $response;
            }
        };
    }
}
