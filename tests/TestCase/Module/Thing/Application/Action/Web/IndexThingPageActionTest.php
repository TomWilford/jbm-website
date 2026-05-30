<?php

declare(strict_types=1);

namespace App\Test\TestCase\Module\Thing\Application\Action\Web;

use App\Module\Thing\Application\Action\Web\IndexThingPageAction;
use App\Test\Traits\AppTestTrait;
use App\Test\Traits\DatabaseTestTrait;
use Fig\Http\Message\StatusCodeInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(IndexThingPageAction::class)]
class IndexThingPageActionTest extends TestCase
{
    use AppTestTrait;
    use DatabaseTestTrait;

    public function testAction(): void
    {
        $request = $this->createRequest('GET', '/things');
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        $this->assertResponseContains('All Things', $response);
        $this->assertResponseContains('Thing 1', $response);
        $this->assertResponseContains('Thing 99', $response);
    }
}
