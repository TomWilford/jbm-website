<?php

use App\Middleware\ExceptionMiddleware;
use Selective\BasePath\BasePathMiddleware;
use Slim\App;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

return function (App $app) {
    $app->addBodyParsingMiddleware();
    $app->addRoutingMiddleware();
    $app->add(BasePathMiddleware::class);
    $app->add(ExceptionMiddleware::class);
    $app->add(TwigMiddleware::create($app, $app->getContainer()?->get(Twig::class)));
    $app->add(new Tuupola\Middleware\HttpBasicAuthentication([
        "path" => "/api",
        "realm" => "Protected",
        "users" => $app->getContainer()?->get('settings')['api']['users']
    ]));
};
