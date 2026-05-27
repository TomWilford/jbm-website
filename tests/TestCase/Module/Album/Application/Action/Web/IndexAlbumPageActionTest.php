<?php

declare(strict_types=1);

namespace App\Test\TestCase\Module\Album\Application\Action\Web;

use App\Module\Album\Application\Action\Web\IndexAlbumPageAction;
use App\Test\Traits\AppTestTrait;
use App\Test\Traits\DatabaseTestTrait;
use Fig\Http\Message\StatusCodeInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(IndexAlbumPageAction::class)]
class IndexAlbumPageActionTest extends TestCase
{
    use AppTestTrait;
    use DatabaseTestTrait;

    public function testAction(): void
    {
        $request = $this->createRequest('GET', '/albums');
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        $this->assertResponseContains('All Snaps', $response);
        $this->assertResponseContains('Album 1', $response);
        $this->assertResponseContains('Album 99', $response);
    }
}
