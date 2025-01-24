<?php

// Define app routes

use Slim\App;

return function (App $app) {
    $app->get('/', \App\Action\Home\Page\HomeAction::class)->setName('home');
    $app->get('/ping', \App\Action\Home\Page\PingAction::class);
    // Things
    // - Page
    $app->get('/things', \App\Action\Things\Page\IndexAction::class)->setName('things.index');
    $app->get('/things/{id:[0-9]+}', \App\Action\Things\Page\ShowAction::class)->setName('things.show');
    // - API
    $app->get('/api/things', \App\Action\Things\Api\IndexAction::class)->setName('api.things.index');
    $app->get('/api/things/{id:[0-9]+}', \App\Action\Things\Api\ShowAction::class)->setName('api.things.show');
    $app->post('/api/things', \App\Action\Things\Api\CreateAction::class)->setName('api.things.create');
    $app->patch('/api/things/{id:[0-9]+}', \App\Action\Things\Api\UpdateAction::class)->setName('api.things.update');
    $app->delete('/api/things/{id:[0-9]+}', \App\Action\Things\Api\DeleteAction::class)->setName('api.things.delete');
    // Bits
    // - Page
    $app->get('/bits', \App\Action\Bits\Page\IndexAction::class)->setName('bits.index');
    $app->get('/bits/{id:[0-9]+}', \App\Action\Bits\Page\ShowAction::class)->setName('bits.show');
    // - API
    $app->get('/api/bits', \App\Action\Bits\Api\IndexAction::class)->setName('api.bits.index');
    $app->get('/api/bits/{id:[0-9]+}', \App\Action\Bits\Api\ShowAction::class)->setName('api.bits.show');
    $app->post('/api/bits', \App\Action\Bits\Api\CreateAction::class)->setName('api.bits.create');
    $app->patch('/api/bits/{id:[0-9]+}', \App\Action\Bits\Api\UpdateAction::class)->setName('api.bits.update');
    $app->delete('/api/bits/{id:[0-9]+}', \App\Action\Bits\Api\DeleteAction::class)->setName('api.bits.delete');
};
