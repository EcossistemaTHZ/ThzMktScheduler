<?php

declare(strict_types=1);

use App\Config\Config;
use App\Core\AppContainer;
use App\Core\Application;
use App\Http\RequestFactory;
use App\Http\ResponseEmitter;
use Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

if (file_exists(dirname(__DIR__) . '/.env')) {
    Dotenv::createImmutable(dirname(__DIR__))->safeLoad();
}

$config = Config::fromEnv();
$container = AppContainer::create($config);
$app = Application::create($container);

$request = RequestFactory::fromGlobals();

ResponseEmitter::emit($app->handle($request));
