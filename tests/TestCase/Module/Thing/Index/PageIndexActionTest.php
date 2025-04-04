<?php

declare(strict_types=1);

namespace App\Test\TestCase\Module\Thing\Index;

use App\Module\Thing\Index\PageIndexAction;
use App\Test\Traits\AppTestTrait;
use App\Test\Traits\DatabaseTestTrait;
use Fig\Http\Message\StatusCodeInterface;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[UsesClass(PageIndexAction::class)]
class PageIndexActionTest extends TestCase
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
