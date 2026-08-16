<?php

declare(strict_types=1);

namespace App\Core;

use App\Action\CampaignAction;
use App\Action\UserAction;
use App\Config\Config;
use App\Http\JsonResponder;
use App\Middleware\CorsMiddleware;
use App\Middleware\ErrorHandlerMiddleware;
use App\Middleware\JsonBodyParserMiddleware;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

final class Application
{
    public static function create(ContainerInterface $container): RequestHandlerInterface
    {
        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $container->get(ResponseFactoryInterface::class);

        /** @var Config $config */
        $config = $container->get(Config::class);

        $campaignAction = $container->get(CampaignAction::class);
        $userAction = $container->get(UserAction::class);
        assert($campaignAction instanceof CampaignAction);
        assert($userAction instanceof UserAction);

        $router = new Router($responseFactory);

        $router->get('/api/v1/campaigns', $campaignAction);
        $router->post('/api/v1/campaigns', $campaignAction);
        $router->get('/api/v1/campaigns/{id}', $campaignAction);
        $router->put('/api/v1/campaigns/{id}', $campaignAction);
        $router->delete('/api/v1/campaigns/{id}', $campaignAction);

        $router->get('/api/v1/users', $userAction);
        $router->post('/api/v1/users', $userAction);
        $router->get('/api/v1/users/{id}', $userAction);
        $router->put('/api/v1/users/{id}', $userAction);
        $router->delete('/api/v1/users/{id}', $userAction);

        return new MiddlewarePipeline([
            new ErrorHandlerMiddleware(
                $container->get(JsonResponder::class),
                $container->get(LoggerInterface::class),
                $config->get('app.debug'),
            ),
            new CorsMiddleware($responseFactory, $config->get('cors.origin')),
            new JsonBodyParserMiddleware($container->get(JsonResponder::class)),
        ], $router);
    }
}
