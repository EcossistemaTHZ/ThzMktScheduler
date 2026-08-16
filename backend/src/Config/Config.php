<?php

declare(strict_types=1);

namespace App\Config;

final class Config
{
    /**
     * @param array<string, mixed> $values
     */
    public function __construct(private readonly array $values)
    {
    }

    public static function fromEnv(): self
    {
        $backendDir = dirname(__DIR__, 2);

        return new self([
            'app.env' => self::env('APP_ENV', 'dev'),
            'app.debug' => filter_var(self::env('APP_DEBUG', 'false'), FILTER_VALIDATE_BOOL),
            'db.dsn' => self::env('DB_DSN', sprintf('sqlite:%s/database.sqlite', $backendDir)),
            'db.user' => self::envOrNull('DB_USER'),
            'db.password' => self::envOrNull('DB_PASS'),
            'cors.origin' => self::env('CORS_ORIGIN', '*'),
            'log.path' => self::env('LOG_PATH', sprintf('%s/var/logs/app.log', $backendDir)),
        ]);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->values[$key] ?? $default;
    }

    private static function env(string $key, string $default): string
    {
        $value = getenv($key);

        return $value === false || $value === '' ? $default : $value;
    }

    private static function envOrNull(string $key): ?string
    {
        $value = getenv($key);

        return $value === false || $value === '' ? null : $value;
    }
}
