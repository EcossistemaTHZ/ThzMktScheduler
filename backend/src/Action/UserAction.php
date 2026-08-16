<?php

declare(strict_types=1);

namespace App\Action;

use App\Http\JsonResponder;
use App\Service\UserService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class UserAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly UserService $users,
        private readonly JsonResponder $responder,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $route = $request->getAttribute('route');
        $id = is_array($route) && isset($route['id']) ? (int) $route['id'] : null;

        return match ($request->getMethod()) {
            'GET' => $id === null
                ? $this->responder->json($this->users->list())
                : $this->responder->json($this->users->show($id)),
            'POST' => $this->create($request),
            'PUT' => $id === null
                ? $this->responder->json(['error' => 'Identificador ausente'], 400)
                : $this->update($id, $request),
            'DELETE' => $id === null
                ? $this->responder->json(['error' => 'Identificador ausente'], 400)
                : $this->delete($id),
            default => $this->responder->json(['error' => 'Method not allowed'], 405),
        };
    }

    private function create(ServerRequestInterface $request): ResponseInterface
    {
        $id = $this->users->create($this->body($request));

        return $this->responder->json(['message' => 'Usuário criado com sucesso', 'id' => $id], 201);
    }

    private function update(int $id, ServerRequestInterface $request): ResponseInterface
    {
        $this->users->update($id, $this->body($request));

        return $this->responder->json(['message' => 'Usuário atualizado com sucesso']);
    }

    private function delete(int $id): ResponseInterface
    {
        $this->users->delete($id);

        return $this->responder->json(['message' => 'Usuário excluído com sucesso']);
    }

    /**
     * @return array<string, mixed>
     */
    private function body(ServerRequestInterface $request): array
    {
        $parsed = $request->getAttribute('parsedBody');

        return is_array($parsed) ? $parsed : [];
    }
}
