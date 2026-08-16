<?php

declare(strict_types=1);

namespace App\Tests;

use App\Config\Config;
use App\Config\Database;
use App\Core\AppContainer;
use App\Core\Application;
use PDO;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Psr\Http\Server\RequestHandlerInterface;

abstract class TestCase extends BaseTestCase
{
    protected function createConfig(): Config
    {
        return new Config([
            'app.env' => 'test',
            'app.debug' => true,
            'db.dsn' => 'sqlite::memory:',
            'db.user' => null,
            'db.password' => null,
            'cors.origin' => '*',
            'log.path' => 'php://stderr',
        ]);
    }

    protected function createPdo(): PDO
    {
        $pdo = Database::connection('sqlite::memory:');
        $this->applySqliteMigrations($pdo);

        return $pdo;
    }

    protected function createApp(): RequestHandlerInterface
    {
        $container = AppContainer::create($this->createConfig());

        /** @var PDO $pdo */
        $pdo = $container->get(PDO::class);
        $this->applySqliteMigrations($pdo);

        return Application::create($container);
    }

    protected function applySqliteMigrations(PDO $pdo): void
    {
        $files = glob(dirname(__DIR__) . '/migrations/sqlite/*.sql') ?: [];
        sort($files, SORT_NATURAL);

        foreach ($files as $file) {
            $sql = file_get_contents($file);
            if ($sql !== false) {
                $pdo->exec($sql);
            }
        }
    }
}
