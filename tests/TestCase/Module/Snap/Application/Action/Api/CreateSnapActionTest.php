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
use Nyholm\Psr7\UploadedFile;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

#[CoversClass(CreateSnapAction::class)]
class CreateSnapActionTest extends TestCase
{
    use AppTestTrait;
    use DatabaseTestTrait;

    /**
     * Helper to load the real physical asset fixture into a PSR-7 standard wrapper.
     */
    private function createUploadedFileFixture(): UploadedFile
    {
        $filePath = dirname(__DIR__, 6) . '/Fixtures/assets/snap-01.webp';
        $stream = new Stream(fopen($filePath, 'r'));
        $size = filesize($filePath) ?: 0;

        return new UploadedFile(
            $stream,
            $size,
            UPLOAD_ERR_OK,
            'snap-01.webp',
            'image/webp'
        );
    }

    public function testAction(): void
    {
        $parsedBody = [
            'album_id' => 1,
        ];
        $uploadedFiles = [
            'image' => $this->createUploadedFileFixture()
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
            'image' => $this->createUploadedFileFixture()
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
                HttpStatus $status
            ) {
                $this->assertSame(['An unknown error occurred. Sorry about that.'], $data);
                $this->assertSame(HttpStatus::INTERNAL_SERVER_ERROR, $status);

                return $response;
            });

        $action = new CreateSnapAction($mockRenderer, $mockCreator);

        $request = (new Psr17Factory())->createServerRequest('POST', '/api/snaps')
            ->withParsedBody(['album_id' => 1])
            ->withUploadedFiles(['image' => $this->createUploadedFileFixture()]);

        $response = (new Psr17Factory())->createResponse();

        $action($request, $response);
    }
}
