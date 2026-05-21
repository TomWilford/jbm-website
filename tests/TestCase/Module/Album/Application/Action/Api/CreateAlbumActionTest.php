<?php

declare(strict_types=1);

namespace App\Test\TestCase\Module\Album\Application\Action\Api;

use App\Application\Renderer\JsonRenderer;
use App\Common\Domain\HttpStatus;
use App\Module\Album\Application\Action\Api\CreateAlbumAction;
use App\Module\Album\Application\Service\CreateAlbum;
use App\Test\Traits\AppTestTrait;
use App\Test\Traits\DatabaseTestTrait;
use Fig\Http\Message\StatusCodeInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

#[CoversClass(CreateAlbumAction::class)]
class CreateAlbumActionTest extends TestCase
{
    use AppTestTrait;
    use DatabaseTestTrait;

    public function testAction(): void
    {
        $formData = [
            'name' => 'Tokyo Street Photography',
            'camera' => 'olympusPen',
            'location' => 'Japan',
            'date' => '2025-04-12',
        ];
        $body = (new Psr17Factory())->createStream(http_build_query($formData));

        $request = $this->createRequest('POST', '/api/albums')
            ->withHeader('Authorization', 'Basic ' . base64_encode('test:test'))
            ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->withBody($body);
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        $this->assertResponseContains('Tokyo Street Photography', $response);
    }

    public function testInvalidData(): void
    {
        $formData = [
            'name' => 'Tokyo Street Photography',
            'camera' => 'noCamera',
            'location' => 'Japan',
            'date' => '2025-04-12',
        ];
        $body = (new Psr17Factory())->createStream(http_build_query($formData));

        $request = $this->createRequest('POST', '/api/albums')
            ->withHeader('Authorization', 'Basic ' . base64_encode('test:test'))
            ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->withBody($body);
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_BAD_REQUEST, $response->getStatusCode());
        $this->assertResponseContains('camera must be in', $response);
    }

    public function testUnexpectedError(): void
    {
        $mockCreator = $this->createMock(CreateAlbum::class);
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
                // Assert the response data and status
                $this->assertSame(['An unknown error occurred. Sorry about that.'], $data);
                $this->assertSame(HttpStatus::INTERNAL_SERVER_ERROR, $status);

                return $response;
            });

        $action = new CreateAlbumAction($mockRenderer, $mockCreator);

        $formData = [
            'name' => 'Tokyo Street Photography',
            'camera' => 'noCamera',
            'location' => 'Japan',
            'date' => '2025-04-12',
        ];
        $body = (new Psr17Factory())->createStream(http_build_query($formData));

        $request = (new Psr17Factory())->createServerRequest('POST', '/api/things')
            ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->withBody($body);

        $response = (new Psr17Factory())->createResponse();

        $action($request, $response);
    }
}
