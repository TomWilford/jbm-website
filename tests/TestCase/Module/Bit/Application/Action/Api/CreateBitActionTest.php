<?php

declare(strict_types=1);

namespace App\Test\TestCase\Module\Bit\Application\Action\Api;

use App\Application\Renderer\JsonRenderer;
use App\Common\Domain\HttpStatus;
use App\Module\Bit\Application\Action\Api\CreateBitAction;
use App\Module\Bit\Application\Service\CreateBit;
use App\Module\Bit\Application\Validator\CreateBitValidator;
use App\Test\Traits\AppTestTrait;
use App\Test\Traits\DatabaseTestTrait;
use Fig\Http\Message\StatusCodeInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

#[CoversClass(CreateBitAction::class)]
class CreateBitActionTest extends TestCase
{
    use AppTestTrait;
    use DatabaseTestTrait;

    public function testAction(): void
    {
        $formData = [
            'name' => 'Test Bit',
            'code' => "var_dump(sprintf('%s %s!', 'Hello', 'World'));",
            'language' => 'PHP',
            'description' => 'Test bit description',
            'returns' => 'string(12) "Hello World!"',
        ];
        $body = (new Psr17Factory())->createStream(http_build_query($formData));

        $request = $this->createRequest('POST', '/api/bits')
            ->withHeader('Authorization', 'Basic ' . base64_encode('test:test'))
            ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->withBody($body);
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        $this->assertResponseContains('Test Bit', $response);
    }

    public function testInvalidData(): void
    {
        $formData = [
            'name' => '', // Invalid: Empty string
            'code' => "var_dump(sprintf('%s %s!', 'Hello', 'World'));",
            'language' => 'PHP',
            'description' => 'Test bit description',
            'returns' => 'string(12) "Hello World!"',
        ];
        $body = (new Psr17Factory())->createStream(http_build_query($formData));

        $request = $this->createRequest('POST', '/api/bits')
            ->withHeader('Authorization', 'Basic ' . base64_encode('test:test'))
            ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->withBody($body);
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_BAD_REQUEST, $response->getStatusCode());
        $this->assertResponseContains('name must have a length between', $response);
    }

    public function testUnexpectedError(): void
    {
        $mockValidator = $this->createMock(CreateBitValidator::class);

        $mockCreator = $this->createMock(CreateBit::class);
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

        $action = new CreateBitAction($mockRenderer, $mockValidator, $mockCreator);

        $formData = [
            'name' => 'Test Bit',
            'code' => "var_dump(sprintf('%s %s!', 'Hello', 'World'));",
            'language' => 'PHP',
            'description' => 'Test bit description',
            'returns' => 'string(12) "Hello World!"',
        ];
        $body = (new Psr17Factory())->createStream(http_build_query($formData));

        $request = (new Psr17Factory())->createServerRequest('POST', '/api/bits')
            ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->withBody($body);

        $response = (new Psr17Factory())->createResponse();

        $action($request, $response);
    }
}
