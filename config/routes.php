<?php

// Define app routes

use Slim\App;

return function (App $app) {
    $app->get('/', \App\Action\Home\HomeAction::class)->setName('home');
    $app->get('/ping', \App\Action\Home\PingAction::class);

    $app->get('/things', \App\Action\Things\IndexAction::class)->setName('things.index');
    $app->get('/things/{id:[0-9]+}', \App\Action\Things\ShowAction::class)->setName('things.show');
};
