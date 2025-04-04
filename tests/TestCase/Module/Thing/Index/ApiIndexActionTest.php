<?php

declare(strict_types=1);

namespace App\Test\TestCase\Module\Thing\Index;

use App\Application\Renderer\JsonRenderer;
use App\Common\Enum\HttpStatus;
use App\Module\Thing\Index\ApiIndexAction;
use App\Module\Thing\Infrastructure\ThingRepository;
use App\Test\Traits\AppTestTrait;
use App\Test\Traits\DatabaseTestTrait;
use Fig\Http\Message\StatusCodeInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

#[UsesClass(ApiIndexAction::class)]
class ApiIndexActionTest extends TestCase
{
    use AppTestTrait;
    use DatabaseTestTrait;

    public function testAction(): void
    {
        $request = $this->createRequest('GET', '/api/things')
            ->withHeader('Authorization', 'Basic ' . base64_encode('test:test'));
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        $this->assertResponseContains('Thing 1', $response);
        $this->assertResponseContains('Thing 99', $response);
    }

    public function testRouteNotFound(): void
    {
        $request = $this->createRequest('GET', '/api/capybara')
            ->withHeader('Authorization', 'Basic ' . base64_encode('test:test'));
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_NOT_FOUND, $response->getStatusCode());
    }

    public function testInvalidCredentials(): void
    {
        $request = $this->createRequest('GET', '/api/test')
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
                // Assert the response data and status
                $this->assertSame(['An unknown error occurred. Sorry about that.'], $data);
                $this->assertSame(HttpStatus::INTERNAL_SERVER_ERROR, $status);

                return $response;
            });

        $mockRepository = $this->createMock(ThingRepository::class);
        $mockRepository->method('all')->willThrowException(new RuntimeException());

        $action = new ApiIndexAction($mockRenderer, $mockRepository);

        $request = (new Psr17Factory())->createServerRequest('GET', '/api/things');

        $response = (new Psr17Factory())->createResponse();

        $action($request, $response);
    }

    public function testEmptyResultSet(): void
    {
        $mockRenderer = $this->createMock(JsonRenderer::class);
        $mockRenderer->expects($this->once())
            ->method('jsonWithStatus')
            ->willReturnCallback(function (
                ResponseInterface $response,
                array $data,
                HttpStatus $status,
            ) {
                // Assert the response data and status
                $this->assertSame([], $data);
                $this->assertSame(HttpStatus::NO_CONTENT, $status);

                return $response;
            });

        $mockRepository = $this->createMock(ThingRepository::class);
        $mockRepository->method('all')->willReturn([]);

        $action = new ApiIndexAction($mockRenderer, $mockRepository);

        $request = (new Psr17Factory())->createServerRequest('GET', '/api/things');

        $response = (new Psr17Factory())->createResponse();

        $action($request, $response);
    }
}
