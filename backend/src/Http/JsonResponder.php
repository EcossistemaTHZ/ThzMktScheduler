<?php

declare(strict_types=1);

namespace App\Http;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

final class JsonResponder
{
    public function __construct(private readonly ResponseFactoryInterface $responseFactory)
    {
    }

    public function json(mixed $data, int $status = 200): ResponseInterface
    {
        $body = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $response = $this->responseFactory->createResponse($status)
            ->withHeader('Content-Type', 'application/json; charset=utf-8');
        $response->getBody()->write($body === false ? '{"error":"Encoding error"}' : $body);

        return $response;
    }
}
