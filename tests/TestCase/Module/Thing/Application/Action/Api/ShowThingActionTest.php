<?php

declare(strict_types=1);

namespace App\Test\TestCase\Module\Thing\Application\Action\Api;

use App\Application\Renderer\JsonRenderer;
use App\Common\Domain\HttpStatus;
use App\Module\Thing\Application\Action\Api\ShowThingAction;
use App\Module\Thing\Infrastructure\ThingRepository;
use App\Test\Traits\AppTestTrait;
use App\Test\Traits\DatabaseTestTrait;
use Fig\Http\Message\StatusCodeInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

#[CoversClass(ShowThingAction::class)]
class ShowThingActionTest extends TestCase
{
    use AppTestTrait;
    use DatabaseTestTrait;

    public function testAction(): void
    {
        $request = $this->createRequest('GET', '/api/things/Uk')
            ->withHeader('Authorization', 'Basic ' . base64_encode('test:test'));
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        $this->assertResponseContains('Thing 1', $response);
    }

    public function testResourceNotFound(): void
    {
        $request = $this->createRequest('GET', '/api/things/404')
            ->withHeader('Authorization', 'Basic ' . base64_encode('test:test'));
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_NOT_FOUND, $response->getStatusCode());
    }

    public function testInvalidCredentials(): void
    {
        $request = $this->createRequest('GET', '/api/things/1')
            ->withHeader('Authorization', 'Basic ' . base64_encode('test:nope'));
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testUnexpectedError(): void
    {
        $mockRenderer = $this->createMock(JsonRenderer::class);
        $mockRenderer->expects($this->once())
            ->method('jsonWithStatus')
            ->willReturnCallback(function (
                ResponseInterface $response,
                array $data,
                HttpStatus $status,
            ) {
                $this->assertSame(['An unknown error occurred. Sorry about that.'], $data);
                $this->assertSame(HttpStatus::INTERNAL_SERVER_ERROR, $status);

                return $response;
            });

        $mockRepository = $this->createMock(ThingRepository::class);
        $mockRepository->method('ofId')->willThrowException(new RuntimeException());

        $action = new ShowThingAction($mockRenderer, $mockRepository);

        $request = (new Psr17Factory())->createServerRequest('GET', '/api/things/23456');

        $response = (new Psr17Factory())->createResponse();

        $action($request, $response, []);
    }
}
