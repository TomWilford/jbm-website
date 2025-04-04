<?php

declare(strict_types=1);

namespace App\Test\TestCase\Module\Bit\Index;

use App\Module\Bit\Index\PageIndexAction;
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
        $request = $this->createRequest('GET', '/bits');
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        $this->assertResponseContains('All Bits', $response);
        $this->assertResponseContains('Test Bit', $response);
        $this->assertResponseContains('Test Bit 99', $response);
    }
}
