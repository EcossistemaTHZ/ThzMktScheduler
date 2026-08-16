<?php

declare(strict_types=1);

namespace App\Config;

use PDO;

final class Database
{
    public static function connection(string $dsn, ?string $user = null, ?string $password = null): PDO
    {
        $pdo = new PDO($dsn, $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        if (str_starts_with($dsn, 'sqlite:')) {
            $pdo->exec('PRAGMA foreign_keys = ON;');
        }

        return $pdo;
    }
}
