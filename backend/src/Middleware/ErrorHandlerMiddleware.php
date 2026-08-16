<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Exception\ConflictException;
use App\Exception\NotFoundException;
use App\Exception\ValidationException;
use App\Http\JsonResponder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Throwable;

final class ErrorHandlerMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly JsonResponder $responder,
        private readonly LoggerInterface $logger,
        private readonly bool $debug = false,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (ValidationException $e) {
            return $this->responder->json([
                'error' => $e->getMessage(),
                'errors' => $e->result->errors(),
            ], 400);
        } catch (NotFoundException $e) {
            return $this->responder->json(['error' => $e->getMessage()], 404);
        } catch (ConflictException $e) {
            return $this->responder->json(['error' => $e->getMessage()], 409);
        } catch (Throwable $e) {
            $this->logger->error($e->getMessage(), [
                'exception' => $e,
                'uri' => (string) $request->getUri(),
            ]);

            $payload = ['error' => 'Internal server error'];
            if ($this->debug) {
                $payload['debug'] = $e->getMessage();
            }

            return $this->responder->json($payload, 500);
        }
    }
}
