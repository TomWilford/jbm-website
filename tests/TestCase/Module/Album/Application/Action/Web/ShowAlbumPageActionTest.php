<?php

declare(strict_types=1);

namespace App\Test\TestCase\Module\Album\Application\Action\Web;

use App\Module\Album\Application\Action\Web\ShowAlbumPageAction;
use App\Test\Traits\AppTestTrait;
use App\Test\Traits\DatabaseTestTrait;
use Fig\Http\Message\StatusCodeInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ShowAlbumPageAction::class)]
class ShowAlbumPageActionTest extends TestCase
{
    use AppTestTrait;
    use DatabaseTestTrait;

    public function testAction(): void
    {
        $request = $this->createRequest('GET', '/albums/Uk');
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        $this->assertResponseContains('Album 1', $response);
    }

    public function testInvalidIdThrows404(): void
    {
        $request = $this->createRequest('GET', '/albums/404');
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_NOT_FOUND, $response->getStatusCode());
    }
}
