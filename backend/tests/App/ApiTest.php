<?php

declare(strict_types=1);

namespace App\Tests\App;

use App\Tests\TestCase;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class ApiTest extends TestCase
{
    private RequestHandlerInterface $app;
    private Psr17Factory $factory;

    protected function setUp(): void
    {
        $this->app = $this->createApp();
        $this->factory = new Psr17Factory();
    }

    public function testCampaignCrudFlow(): void
    {
        $create = $this->request('POST', '/api/v1/campaigns', [
            'subject' => 'Lançamento',
            'message' => 'Novo produto disponível',
            'scheduled_at' => '2030-01-01T10:00',
        ]);
        $created = $this->app->handle($create);

        self::assertSame(201, $created->getStatusCode());
        $createdBody = json_decode((string) $created->getBody(), true);
        self::assertSame('Campanha criada com sucesso', $createdBody['message']);
        $id = $createdBody['id'];

        $list = $this->app->handle($this->request('GET', '/api/v1/campaigns'));
        self::assertSame(200, $list->getStatusCode());
        $listBody = json_decode((string) $list->getBody(), true);
        self::assertCount(1, $listBody);
        self::assertSame('Lançamento', $listBody[0]['subject']);

        $show = $this->app->handle($this->request('GET', '/api/v1/campaigns/' . $id));
        self::assertSame(200, $show->getStatusCode());

        $update = $this->app->handle($this->request('PUT', '/api/v1/campaigns/' . $id, ['status' => 'sent']));
        self::assertSame(200, $update->getStatusCode());

        $delete = $this->app->handle($this->request('DELETE', '/api/v1/campaigns/' . $id));
        self::assertSame(200, $delete->getStatusCode());

        $gone = $this->app->handle($this->request('GET', '/api/v1/campaigns/' . $id));
        self::assertSame(404, $gone->getStatusCode());
    }

    public function testCreateCampaignValidationError(): void
    {
        $response = $this->app->handle($this->request('POST', '/api/v1/campaigns', ['subject' => '']));

        self::assertSame(400, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        self::assertArrayHasKey('errors', $body);
    }

    public function testUserDuplicateEmailReturnsConflict(): void
    {
        $this->app->handle($this->request('POST', '/api/v1/users', ['name' => 'João', 'email' => 'joao@example.com']));

        $response = $this->app->handle($this->request('POST', '/api/v1/users', ['name' => 'Bia', 'email' => 'joao@example.com']));

        self::assertSame(409, $response->getStatusCode());
    }

    public function testUserInvalidEmailReturnsBadRequest(): void
    {
        $response = $this->app->handle($this->request('POST', '/api/v1/users', ['name' => 'João', 'email' => 'não-e-email']));

        self::assertSame(400, $response->getStatusCode());
    }

    public function testUnknownRouteReturnsNotFound(): void
    {
        $response = $this->app->handle($this->request('GET', '/api/v1/desconhecido'));

        self::assertSame(404, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        self::assertSame('Route not found', $body['error']);
    }

    public function testInvalidJsonBodyReturnsBadRequest(): void
    {
        $request = $this->factory->createServerRequest('POST', '/api/v1/campaigns')
            ->withHeader('Content-Type', 'application/json')
            ->withBody($this->factory->createStream('{invalido'));

        $response = $this->app->handle($request);

        self::assertSame(400, $response->getStatusCode());
    }

    public function testCorsPreflightIsHandled(): void
    {
        $request = $this->factory->createServerRequest('OPTIONS', '/api/v1/campaigns');

        $response = $this->app->handle($request);

        self::assertSame(204, $response->getStatusCode());
        self::assertSame('*', $response->getHeaderLine('Access-Control-Allow-Origin'));
    }

    /**
     * @param array<string, mixed>|null $body
     */
    private function request(string $method, string $path, ?array $body = null): ServerRequestInterface
    {
        $request = $this->factory->createServerRequest($method, $path);

        if ($body !== null) {
            $request = $request
                ->withHeader('Content-Type', 'application/json')
                ->withBody($this->factory->createStream((string) json_encode($body)));
        }

        return $request;
    }
}
