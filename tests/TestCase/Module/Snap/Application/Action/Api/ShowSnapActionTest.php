<?php

declare(strict_types=1);

namespace App\Test\TestCase\Module\Snap\Application\Action\Api;

use App\Application\Renderer\JsonRenderer;
use App\Common\Domain\HttpStatus;
use App\Module\Snap\Application\Action\Api\ShowSnapAction;
use App\Module\Snap\Infrastructure\SnapRepository;
use App\Test\Traits\AppTestTrait;
use App\Test\Traits\DatabaseTestTrait;
use Fig\Http\Message\StatusCodeInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

#[CoversClass(ShowSnapAction::class)]
class ShowSnapActionTest extends TestCase
{
    use AppTestTrait;
    use DatabaseTestTrait;

    public function testAction(): void
    {
        $request = $this->createRequest('GET', '/api/snaps/Uk')
            ->withHeader('Authorization', 'Basic ' . base64_encode('test:test'));
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        $this->assertResponseContains('image/webp', $response);
    }

    public function testResourceNotFound(): void
    {
        $request = $this->createRequest('GET', '/api/snaps/404')
            ->withHeader('Authorization', 'Basic ' . base64_encode('test:test'));
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_NOT_FOUND, $response->getStatusCode());
    }

    public function testInvalidCredentials(): void
    {
        $request = $this->createRequest('GET', '/api/snaps/1')
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

        $mockRepository = $this->createMock(SnapRepository::class);
        $mockRepository->method('ofId')->willThrowException(new RuntimeException());

        $action = new ShowSnapAction($mockRenderer, $mockRepository);

        $request = (new Psr17Factory())->createServerRequest('GET', '/api/snaps/23456');

        $response = (new Psr17Factory())->createResponse();

        $action($request, $response, []);
    }
}
