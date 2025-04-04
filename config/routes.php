<?php

// Define app routes

use Slim\App;

return function (App $app) {
    $app->get('/', \App\Application\Action\HomeAction::class)->setName('home');
    $app->get('/ping', \App\Application\Action\PingAction::class);
    // Things
    // - Page
    $app->get('/things', \App\Module\Thing\Index\PageIndexAction::class)->setName('things.index');
    $app->get('/things/{sqid}', \App\Module\Thing\Show\PageShowAction::class)->setName('things.show');
    // - API
    $app->get('/api/things', \App\Module\Thing\Index\ApiIndexAction::class)->setName('api.things.index');
    $app->get('/api/things/{sqid}', \App\Module\Thing\Show\ApiShowAction::class)->setName('api.things.show');
    $app->post('/api/things', \App\Module\Thing\Create\Application\ApiCreateAction::class)->setName('api.things.create');
    $app->patch('/api/things/{sqid}', \App\Module\Thing\Update\Application\ApiUpdateAction::class)->setName('api.things.update');
    $app->delete('/api/things/{sqid}', \App\Module\Thing\Delete\ApiDeleteAction::class)->setName('api.things.delete');
    // Bits
    // - Page
    $app->get('/bits', \App\Module\Bit\Index\PageIndexAction::class)->setName('bits.index');
    $app->get('/bits/{sqid}', \App\Module\Bit\Show\PageShowAction::class)->setName('bits.show');
    // - API
    $app->get('/api/bits', \App\Module\Bit\Index\ApiIndexAction::class)->setName('api.bits.index');
    $app->get('/api/bits/{sqid}', \App\Module\Bit\Show\ApiShowAction::class)->setName('api.bits.show');
    $app->post('/api/bits', \App\Module\Bit\Create\Application\ApiCreateAction::class)->setName('api.bits.create');
    $app->patch('/api/bits/{sqid}', \App\Module\Bit\Update\Application\ApiUpdateAction::class)->setName('api.bits.update');
    $app->delete('/api/bits/{sqid}', \App\Module\Bit\Delete\ApiDeleteAction::class)->setName('api.bits.delete');
};
