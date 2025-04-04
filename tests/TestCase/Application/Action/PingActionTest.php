<?php

declare(strict_types=1);

namespace App\Test\TestCase\Application\Action;

use App\Application\Action\PingAction;
use App\Test\Traits\AppTestTrait;
use Fig\Http\Message\StatusCodeInterface;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[UsesClass(PingAction::class)]
class PingActionTest extends TestCase
{
    use AppTestTrait;

    public function testAction(): void
    {
        $request = $this->createRequest('GET', '/ping');
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        $this->assertResponseContains('{"success":true}', $response);
    }
}
