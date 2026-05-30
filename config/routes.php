<?php

declare(strict_types=1);

// Define app routes

use App\Application\Action\HomeAction;
use App\Application\Action\PingAction;
use App\Module\Album\Application\Action\Api\CreateAlbumAction;
use App\Module\Album\Application\Action\Api\DeleteAlbumAction;
use App\Module\Album\Application\Action\Api\IndexAlbumAction;
use App\Module\Album\Application\Action\Api\ShowAlbumAction;
use App\Module\Album\Application\Action\Web\IndexAlbumPageAction;
use App\Module\Album\Application\Action\Web\ShowAlbumPageAction;
use App\Module\Bit\Application\Action\Api\CreateBitAction;
use App\Module\Bit\Application\Action\Api\DeleteBitAction;
use App\Module\Bit\Application\Action\Api\IndexBitAction;
use App\Module\Bit\Application\Action\Api\ShowBitAction;
use App\Module\Bit\Application\Action\Api\UpdateBitAction;
use App\Module\Bit\Application\Action\Web\IndexBitPageAction;
use App\Module\Bit\Application\Action\Web\ShowBitPageAction;
use App\Module\Snap\Application\Action\Api\CreateSnapAction;
use App\Module\Snap\Application\Action\Api\DeleteSnapAction;
use App\Module\Snap\Application\Action\Api\ShowSnapAction;
use App\Module\Snap\Application\Action\Web\ShowSnapFileAction;
use App\Module\Snap\Application\Action\Web\ShowSnapPageAction;
use App\Module\Thing\Application\Action\Api\CreateThingAction;
use App\Module\Thing\Application\Action\Api\DeleteThingAction;
use App\Module\Thing\Application\Action\Api\IndexThingAction;
use App\Module\Thing\Application\Action\Api\ShowThingAction;
use App\Module\Thing\Application\Action\Api\UpdateThingAction;
use App\Module\Thing\Application\Action\Web\IndexThingPageAction;
use App\Module\Thing\Application\Action\Web\ShowThingPageAction;
use Slim\App;

return function (App $app) {
    $app->get('/', HomeAction::class)->setName('home');
    $app->get('/ping', PingAction::class);

    // Things
    // // Web
    $app->get('/things', IndexThingPageAction::class)->setName('things.index');
    $app->get('/things/{sqid}', ShowThingPageAction::class)->setName('things.show');
    // // API
    $app->get('/api/things', IndexThingAction::class)->setName('api.things.index');
    $app->get('/api/things/{sqid}', ShowThingAction::class)->setName('api.things.show');
    $app->post('/api/things', CreateThingAction::class)->setName('api.things.create');
    $app->patch('/api/things/{sqid}', UpdateThingAction::class)->setName('api.things.update');
    $app->delete('/api/things/{sqid}', DeleteThingAction::class)->setName('api.things.delete');

    // Bits
    // // Web
    $app->get('/bits', IndexBitPageAction::class)->setName('bits.index');
    $app->get('/bits/{sqid}', ShowBitPageAction::class)->setName('bits.show');
    // // API
    $app->get('/api/bits', IndexBitAction::class)->setName('api.bits.index');
    $app->get('/api/bits/{sqid}', ShowBitAction::class)->setName('api.bits.show');
    $app->post('/api/bits', CreateBitAction::class)->setName('api.bits.create');
    $app->patch('/api/bits/{sqid}', UpdateBitAction::class)->setName('api.bits.update');
    $app->delete('/api/bits/{sqid}', DeleteBitAction::class)->setName('api.bits.delete');

    // Albums
    // // Web
    $app->get('/albums', IndexAlbumPageAction::class)->setName('albums.index');
    $app->get('/albums/{sqid}', ShowAlbumPageAction::class)->setName('albums.show');
    // // API
    $app->get('/api/albums', IndexAlbumAction::class)->setName('api.albums.index');
    $app->get('/api/albums/{sqid}', ShowAlbumAction::class)->setName('api.albums.show');
    $app->post('/api/albums', CreateAlbumAction::class)->setName('api.albums.create');
    $app->delete('/api/albums/{sqid}', DeleteAlbumAction::class)->setName('api.album.delete');

    // Snaps
    // // Web
    $app->get('/snaps/{filename:[0-9A-z]+\.[a-z]{3,4}}', ShowSnapFileAction::class)->setName('snaps.file');
    $app->get('/snaps/{sqid}', ShowSnapPageAction::class)->setName('snaps.show');
    // // API
    $app->get('/api/snaps/{sqid}', ShowSnapAction::class)->setName('api.snaps.show');
    $app->post('/api/snaps', CreateSnapAction::class)->setName('api.snaps.create');
    $app->delete('/api/snaps/{sqid}', DeleteSnapAction::class)->setName('api.snaps.delete');
};
