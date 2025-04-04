<?php

declare(strict_types=1);

namespace App\Test\TestCase\Module\Thing\Show;

use App\Module\Thing\Show\PageShowAction;
use App\Test\Traits\AppTestTrait;
use App\Test\Traits\DatabaseTestTrait;
use Fig\Http\Message\StatusCodeInterface;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[UsesClass(PageShowAction::class)]
class PageShowActionTest extends TestCase
{
    use AppTestTrait;
    use DatabaseTestTrait;

    public function testAction(): void
    {
        $request = $this->createRequest('GET', '/things/Uk');
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        $this->assertResponseContains('Thing 1', $response);
    }

    public function testInvalidIdThrows404(): void
    {
        $request = $this->createRequest('GET', '/things/404');
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_NOT_FOUND, $response->getStatusCode());
    }
}
