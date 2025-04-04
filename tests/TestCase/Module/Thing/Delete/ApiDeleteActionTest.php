<?php

declare(strict_types=1);

namespace App\Test\TestCase\Module\Thing\Delete;

use App\Application\Renderer\JsonRenderer;
use App\Common\Enum\HttpStatus;
use App\Domain\Exception\DomainRecordNotFoundException;
use App\Module\Thing\Data\Thing;
use App\Module\Thing\Delete\ApiDeleteAction;
use App\Module\Thing\Enum\FaultLevel;
use App\Module\Thing\Infrastructure\ThingRepository;
use App\Test\Traits\AppTestTrait;
use App\Test\Traits\DatabaseTestTrait;
use Doctrine\DBAL\Connection;
use Fig\Http\Message\StatusCodeInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

#[UsesClass(ApiDeleteAction::class)]
class ApiDeleteActionTest extends TestCase
{
    use AppTestTrait;
    use DatabaseTestTrait;

    public function testAction(): void
    {
        $repository = new ThingRepository($this->container?->get(Connection::class));

        $thing = new Thing(
            null,
            'Initial Name',
            'Initial Short Description',
            'Initial Description',
            true,
            FaultLevel::ALL,
            -3600,
            3155760000,
            'https://example.com',
            1736881351,
            1736881351
        );

        $thing = $repository->store($thing);

        $request = $this->createRequest('DELETE', '/api/things/' . $thing->getSqid())
            ->withHeader('Authorization', 'Basic ' . base64_encode('test:test'));
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        $this->assertResponseContains('Thing deleted successfully', $response);

        $this->expectException(DomainRecordNotFoundException::class);
        $repository->ofId($thing->getId());
    }

    public function testDeleteUnknownId(): void
    {
        $request = $this->createRequest('DELETE', '/api/things/909090')
            ->withHeader('Authorization', 'Basic ' . base64_encode('test:test'));
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_NOT_FOUND, $response->getStatusCode());
        $this->assertResponseContains('Thing not found', $response);
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
        $mockRepository->method('destroy')->willThrowException(new RuntimeException());

        $action = new ApiDeleteAction($mockRenderer, $mockRepository);

        $request = (new Psr17Factory())->createServerRequest('DELETE', '/api/things/23456');

        $response = (new Psr17Factory())->createResponse();

        $action($request, $response, []);
    }
}
