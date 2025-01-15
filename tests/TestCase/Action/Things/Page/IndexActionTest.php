<?php

declare(strict_types=1);

namespace App\Test\TestCase\Action\Things\Page;

use App\Action\Things\Page\IndexAction;
use App\Test\Traits\AppTestTrait;
use App\Test\Traits\DatabaseTestTrait;
use Fig\Http\Message\StatusCodeInterface;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[UsesClass(IndexAction::class)]
class IndexActionTest extends TestCase
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
