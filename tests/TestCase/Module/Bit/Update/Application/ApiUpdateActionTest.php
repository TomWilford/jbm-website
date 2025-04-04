<?php

declare(strict_types=1);

namespace App\Test\TestCase\Module\Bit\Update\Application;

use App\Application\Renderer\JsonRenderer;
use App\Common\Enum\HttpStatus;
use App\Module\Bit\Data\Bit;
use App\Module\Bit\Enum\Language;
use App\Module\Bit\Infrastructure\BitRepository;
use App\Module\Bit\Update\Application\ApiUpdateAction;
use App\Module\Bit\Update\Domain\BitUpdater;
use App\Module\Bit\Update\Domain\UpdateBitValidator;
use App\Test\Traits\AppTestTrait;
use App\Test\Traits\DatabaseTestTrait;
use Doctrine\DBAL\Connection;
use Fig\Http\Message\StatusCodeInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

#[UsesClass(ApiUpdateAction::class)]
class ApiUpdateActionTest extends TestCase
{
    use AppTestTrait;
    use DatabaseTestTrait;

    public function testAction(): void
    {
        $repository = new BitRepository($this->container?->get(Connection::class));

        $bit = new Bit(
            null,
            'Initial Name',
            'Initial Code',
            Language::PHP,
            'Initial Description',
            'Initial Returns'
        );

        $bit = $repository->store($bit);

        $formData = [
            'name' => 'New Name',
            'code' => 'New Code',
            'language' => 'MIXED',
            'description' => 'New Description',
            'returns' => 'New Returns',
        ];
        $body = (new Psr17Factory())->createStream(http_build_query($formData));

        $request = $this->createRequest('PATCH', '/api/bits/' . $bit->getSqid())
            ->withHeader('Authorization', 'Basic ' . base64_encode('test:test'))
            ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->withBody($body);
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        $this->assertResponseContains((string)$bit->getSqid(), $response);
        $this->assertResponseContains('New Name', $response);
        $this->assertResponseContains('New Code', $response);
        $this->assertResponseContains('MIXED', $response);
        $this->assertResponseContains('New Description', $response);
        $this->assertResponseContains('New Returns', $response);
    }

    public function testInvalidIdInRequest(): void
    {
        $formData = [
            'name' => 'New Name',
            'code' => 'New Code',
            'language' => 'MIXED',
            'description' => 'New Description',
            'returns' => 'New Returns',
        ];
        $body = (new Psr17Factory())->createStream(http_build_query($formData));

        $request = $this->createRequest('PATCH', '/api/bits/345435')
            ->withHeader('Authorization', 'Basic ' . base64_encode('test:test'))
            ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->withBody($body);
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_NOT_FOUND, $response->getStatusCode());
    }

    public function testInvalidData(): void
    {
        $formData = [
            'name' => 'New Name',
            'code' => 'New Code',
            'language' => 'ESPERANTO', // Invalid language option
            'description' => 'New Description',
            'returns' => 'New Returns',
        ];
        $body = (new Psr17Factory())->createStream(http_build_query($formData));

        $request = $this->createRequest('PATCH', '/api/bits/Uk')
            ->withHeader('Authorization', 'Basic ' . base64_encode('test:test'))
            ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->withBody($body);
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_BAD_REQUEST, $response->getStatusCode());
        $this->assertResponseContains('language must be in', $response);
    }

    public function testUnexpectedError(): void
    {
        $mockRepository = $this->createMock(BitRepository::class);

        $mockValidator = $this->createMock(UpdateBitValidator::class);

        $mockCreator = $this->createMock(BitUpdater::class);
        $mockCreator->method('updateFromArray')
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

        $action = new ApiUpdateAction($mockRenderer, $mockRepository, $mockValidator, $mockCreator);

        $formData = [
            'name' => 'New Name',
            'code' => 'New Code',
            'language' => 'MIXED',
            'description' => 'New Description',
            'returns' => 'New Returns',
        ];
        $body = (new Psr17Factory())->createStream(http_build_query($formData));

        $request = (new Psr17Factory())->createServerRequest('PUT', '/api/bits/Uk')
            ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->withBody($body);

        $response = (new Psr17Factory())->createResponse();

        $action($request, $response, ['sqid' => '1']);
    }
}
