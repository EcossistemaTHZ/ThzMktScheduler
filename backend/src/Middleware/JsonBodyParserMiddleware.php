<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Http\JsonResponder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class JsonBodyParserMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly JsonResponder $responder)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $body = (string) $request->getBody();

        if ($body === '' || !str_contains(strtolower($request->getHeaderLine('Content-Type')), 'json')) {
            return $handler->handle($request);
        }

        $parsed = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->responder->json(['error' => 'Corpo JSON inválido'], 400);
        }

        return $handler->handle(
            $request->withAttribute('parsedBody', is_array($parsed) ? $parsed : []),
        );
    }
}
