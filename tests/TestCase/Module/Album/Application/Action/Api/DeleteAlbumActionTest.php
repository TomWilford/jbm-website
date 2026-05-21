<?php

declare(strict_types=1);

namespace App\Test\TestCase\Module\Album\Application\Action\Api;

use App\Application\Renderer\JsonRenderer;
use App\Common\Domain\HttpStatus;
use App\Infrastructure\Exception\DomainRecordNotFoundException;
use App\Module\Album\Application\Action\Api\DeleteAlbumAction;
use App\Module\Album\Domain\Album;
use App\Module\Album\Domain\Camera;
use App\Module\Album\Infrastructure\AlbumRepository;
use App\Test\Traits\AppTestTrait;
use App\Test\Traits\DatabaseTestTrait;
use Doctrine\DBAL\Connection;
use Fig\Http\Message\StatusCodeInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

#[CoversClass(DeleteAlbumAction::class)]
class DeleteAlbumActionTest extends TestCase
{
    use AppTestTrait;
    use DatabaseTestTrait;

    public function testAction(): void
    {
        $repository = new AlbumRepository($this->container?->get(Connection::class));

        $album = new Album(
            null,
            'Tokyo Street Photography',
            Camera::OLYMPUS_PEN,
            'Japan',
            '2025-04-12',
        );

        $album = $repository->store($album);

        $request = $this->createRequest('DELETE', '/api/albums/' . $album->getSqid())
            ->withHeader('Authorization', 'Basic ' . base64_encode('test:test'));
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        $this->assertResponseContains('Album deleted successfully', $response);

        $this->expectException(DomainRecordNotFoundException::class);
        $repository->ofId($album->getId());
    }

    public function testDeleteUnknownId(): void
    {
        $request = $this->createRequest('DELETE', '/api/albums/909090')
            ->withHeader('Authorization', 'Basic ' . base64_encode('test:test'));
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_NOT_FOUND, $response->getStatusCode());
        $this->assertResponseContains('Album not found', $response);
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

        $mockRepository = $this->createMock(AlbumRepository::class);
        $mockRepository->method('destroy')->willThrowException(new RuntimeException());

        $action = new DeleteAlbumAction($mockRenderer, $mockRepository);

        $request = (new Psr17Factory())->createServerRequest('DELETE', '/api/album/23456');

        $response = (new Psr17Factory())->createResponse();

        $action($request, $response, []);
    }
}
