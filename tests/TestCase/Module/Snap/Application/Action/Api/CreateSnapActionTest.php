<?php

declare(strict_types=1);

namespace App\Test\TestCase\Module\Snap\Application\Action\Api;

use App\Application\Renderer\JsonRenderer;
use App\Common\Domain\HttpStatus;
use App\Module\Snap\Application\Action\Api\CreateSnapAction;
use App\Module\Snap\Application\Service\CreateSnap;
use App\Test\Traits\AppTestTrait;
use App\Test\Traits\DatabaseTestTrait;
use Fig\Http\Message\StatusCodeInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Stream;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;

#[CoversClass(CreateSnapAction::class)]
class CreateSnapActionTest extends TestCase
{
    use AppTestTrait;
    use DatabaseTestTrait;

    private function createMockUploadedFile(
        int $size = 51200,
        int $error = UPLOAD_ERR_OK,
        string $clientFilename = 'test.webp',
        string $mediaType = 'image/webp',
    ): UploadedFileInterface {
        $filePath = dirname(__DIR__, 6) . '/Fixtures/assets/snap-01.webp';

        return (new Psr17Factory())->createUploadedFile(
            new Stream(fopen($filePath, 'r+')),
            $size,
            $error,
            $clientFilename,
            $mediaType
        );
    }

    public function testAction(): void
    {
        $parsedBody = [
            'album_sqid' => 'Uk',
        ];
        $uploadedFiles = [
            'image' => $this->createMockUploadedFile(),
        ];

        $request = $this->createRequest('POST', '/api/snaps')
            ->withHeader('Authorization', 'Basic ' . base64_encode('test:test'))
            ->withParsedBody($parsedBody)
            ->withUploadedFiles($uploadedFiles);
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        $this->assertResponseContains('"album_id":1', $response);
        $this->assertResponseContains('"mime_type":"image/webp"', $response);
    }

    public function testInvalidData(): void
    {
        $parsedBody = [
            'album_id' => '',
        ];
        $uploadedFiles = [
            'image' => $this->createMockUploadedFile(),
        ];

        $request = $this->createRequest('POST', '/api/snaps')
            ->withHeader('Authorization', 'Basic ' . base64_encode('test:test'))
            ->withParsedBody($parsedBody)
            ->withUploadedFiles($uploadedFiles);

        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_BAD_REQUEST, $response->getStatusCode());
    }

    public function testUnexpectedError(): void
    {
        $mockCreator = $this->createMock(CreateSnap::class);
        $mockCreator->method('createFromArray')
            ->willThrowException(new RuntimeException());

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

        $action = new CreateSnapAction($mockRenderer, $mockCreator);

        $request = (new Psr17Factory())->createServerRequest('POST', '/api/snaps')
            ->withParsedBody(['album_id' => 'Uk'])
            ->withUploadedFiles(['image' => $this->createMockUploadedFile()]);

        $response = (new Psr17Factory())->createResponse();

        $action($request, $response);
    }
}
