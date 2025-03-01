<?php

declare(strict_types=1);

namespace App\Test\TestCase\Action\Bits\Api;

use App\Action\Bits\Api\DeleteAction;
use App\Domain\Bit\Bit;
use App\Domain\Bit\Enum\Language;
use App\Domain\Bit\Repository\BitRepository;
use App\Domain\Exception\DomainRecordNotFoundException;
use App\Infrastructure\Enum\HttpStatus;
use App\Renderer\JsonRenderer;
use App\Test\Traits\AppTestTrait;
use App\Test\Traits\DatabaseTestTrait;
use Doctrine\DBAL\Connection;
use Fig\Http\Message\StatusCodeInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

#[UsesClass(DeleteAction::class)]
class DeleteActionTest extends TestCase
{
    use AppTestTrait;
    use DatabaseTestTrait;

    public function testAction(): void
    {
        $repository = new BitRepository($this->container?->get(Connection::class));

        $bit = new Bit(
            id: null,
            name: 'Test Bit',
            code: "var_dump(sprintf('%s %s!', 'Hello', 'World'));",
            language: Language::PHP,
            description: 'Test description',
            returns: 'string(12) "Hello World!"'
        );

        $bit = $repository->store($bit);

        $request = $this->createRequest('DELETE', '/api/bits/' . $bit->getSqid())
            ->withHeader('Authorization', 'Basic ' . base64_encode('test:test'));
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        $this->assertResponseContains('Bit deleted successfully', $response);

        $this->expectException(DomainRecordNotFoundException::class);
        $repository->ofId($bit->getId());
    }

    public function testDeleteUnknownId(): void
    {
        $request = $this->createRequest('DELETE', '/api/bits/909090')
            ->withHeader('Authorization', 'Basic ' . base64_encode('test:test'));
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_NOT_FOUND, $response->getStatusCode());
        $this->assertResponseContains('Bit not found', $response);
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

        $mockRepository = $this->createMock(BitRepository::class);
        $mockRepository->method('destroy')->willThrowException(new RuntimeException());

        $action = new DeleteAction($mockRenderer, $mockRepository);

        $request = (new Psr17Factory())->createServerRequest('DELETE', '/api/bits/23456');

        $response = (new Psr17Factory())->createResponse();

        $action($request, $response, []);
    }
}
