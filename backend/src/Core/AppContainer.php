<?php

declare(strict_types=1);

namespace App\Core;

use App\Config\Config;
use App\Config\Database;
use DI\ContainerBuilder;

use function DI\factory;

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Nyholm\Psr7\Factory\Psr17Factory;
use PDO;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Log\LoggerInterface;

final class AppContainer
{
    public static function create(Config $config): ContainerInterface
    {
        $builder = new ContainerBuilder();

        $builder->addDefinitions([
            Config::class => $config,
            Psr17Factory::class => factory(static fn (): Psr17Factory => new Psr17Factory()),
            ResponseFactoryInterface::class => factory(static fn (): Psr17Factory => new Psr17Factory()),
            PDO::class => factory(static fn (Config $c): PDO => Database::connection(
                $c->get('db.dsn'),
                $c->get('db.user'),
                $c->get('db.password'),
            )),
            LoggerInterface::class => factory(static function (Config $c): LoggerInterface {
                $logger = new Logger('app');
                $handler = $c->get('app.env') === 'dev'
                    ? new StreamHandler('php://stderr', Level::Debug)
                    : new StreamHandler($c->get('log.path'), Level::Info);
                $logger->pushHandler($handler);

                return $logger;
            }),
        ]);

        return $builder->build();
    }
}
