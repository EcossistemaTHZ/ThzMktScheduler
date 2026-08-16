<?php

declare(strict_types=1);

namespace App\Http;

use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ServerRequestInterface;

final class RequestFactory
{
    public static function fromGlobals(): ServerRequestInterface
    {
        $factory = new Psr17Factory();
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = $factory->createUri((string) ($_SERVER['REQUEST_URI'] ?? '/'));

        $request = $factory->createServerRequest($method, $uri, $_SERVER);

        foreach (self::headersFromServer($_SERVER) as $name => $value) {
            $request = $request->withAddedHeader($name, $value);
        }

        $body = file_get_contents('php://input');
        $request = $request->withBody($factory->createStream($body === false ? '' : $body));

        $query = [];
        parse_str($uri->getQuery(), $query);

        return $request
            ->withQueryParams($query)
            ->withCookieParams($_COOKIE);
    }

    /**
     * getallheaders() não existe no servidor embutido do PHP, então as
     * cabeçalhos são reconstruídos a partir de $_SERVER (convenção HTTP_*).
     *
     * @param array<string, mixed> $server
     * @return array<string, string>
     */
    private static function headersFromServer(array $server): array
    {
        $headers = [];

        foreach ($server as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $name = strtolower(str_replace('_', '-', substr($key, 5)));
                $headers[$name] = (string) $value;
            }
        }

        if (isset($server['CONTENT_TYPE'])) {
            $headers['content-type'] = (string) $server['CONTENT_TYPE'];
        }
        if (isset($server['CONTENT_LENGTH'])) {
            $headers['content-length'] = (string) $server['CONTENT_LENGTH'];
        }

        return $headers;
    }
}
