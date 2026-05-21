<?php

declare(strict_types=1);

namespace App\Test\TestCase\Module\Bit\Application\Action\Page;

use App\Module\Bit\Application\Action\Page\IndexBitPageAction;
use App\Test\Traits\AppTestTrait;
use App\Test\Traits\DatabaseTestTrait;
use Fig\Http\Message\StatusCodeInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(IndexBitPageAction::class)]
class IndexBitPageActionTest extends TestCase
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
