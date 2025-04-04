<?php

use App\Application\Middleware\ExceptionMiddleware;
use Selective\BasePath\BasePathMiddleware;
use Slim\App;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use TomWilford\SlimSqids\SqidsMiddleware;

return function (App $app) {
    $app->addBodyParsingMiddleware();
    $app->add(SqidsMiddleware::class);
    $app->addRoutingMiddleware();
    $app->add(BasePathMiddleware::class);
    $app->add(ExceptionMiddleware::class);
    $app->add(TwigMiddleware::create($app, $app->getContainer()?->get(Twig::class)));
    $app->add(new Tuupola\Middleware\HttpBasicAuthentication($app->getContainer()?->get('settings')['api']));
};
