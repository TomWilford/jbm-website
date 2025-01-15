<?php

// Define app routes

use App\Action\Things\Api\DeleteAction;
use Slim\App;

return function (App $app) {
    $app->get('/', \App\Action\Home\Page\HomeAction::class)->setName('home');
    $app->get('/ping', \App\Action\Home\Page\PingAction::class);

    $app->get('/things', \App\Action\Things\Page\IndexAction::class)->setName('things.index');
    $app->get('/things/{id:[0-9]+}', \App\Action\Things\Page\ShowAction::class)->setName('things.show');

    $app->get('/api/things', \App\Action\Things\Api\IndexAction::class)->setName('api.things.index');
    $app->get('/api/things/{id:[0-9]+}', \App\Action\Things\Api\ShowAction::class)->setName('api.things.show');
    $app->post('/api/things', \App\Action\Things\Api\CreateAction::class)->setName('api.things.create');
    $app->patch('/api/things/{id:[0-9]+}', \App\Action\Things\Api\UpdateAction::class)->setName('api.things.update');
    $app->delete('/api/things/{id:[0-9]+}', DeleteAction::class)->setName('api.things.delete');
};
