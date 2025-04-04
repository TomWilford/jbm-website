<?php

declare(strict_types=1);

namespace App\Test\TestCase\Module\Thing\Update\Application;

use App\Application\Renderer\JsonRenderer;
use App\Common\Enum\HttpStatus;
use App\Module\Thing\Data\Thing;
use App\Module\Thing\Enum\FaultLevel;
use App\Module\Thing\Infrastructure\ThingRepository;
use App\Module\Thing\Update\Application\ApiUpdateAction;
use App\Module\Thing\Update\Domain\ThingUpdater;
use App\Module\Thing\Update\Domain\UpdateThingValidator;
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

        $formData = [
            'name' => 'New Name',
            'short_description' => 'New short description',
            'description' => 'New long description',
            'featured' => 0,
            'url' => 'https://example.co.uk',
            'fault_level' => 'most',
            'active_from' => '1970-01-01',
            'active_to' => '2070-01-01',
        ];
        $body = (new Psr17Factory())->createStream(http_build_query($formData));

        $request = $this->createRequest('PATCH', '/api/things/' . $thing->getSqid())
            ->withHeader('Authorization', 'Basic ' . base64_encode('test:test'))
            ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->withBody($body);
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        $this->assertResponseContains((string)$thing->getSqid(), $response);
        $this->assertResponseContains('New Name', $response);
        $this->assertResponseContains('New short description', $response);
        $this->assertResponseContains('New long description', $response);
        $this->assertResponseContains('"featured":false', $response);
        $this->assertResponseContains('https://example.co.uk', $response);
        $this->assertResponseContains('most', $response);
        $this->assertResponseContains('-3600', $response);
        $this->assertResponseContains('3155760000', $response);
    }

    public function testInvalidIdInRequest(): void
    {
        $formData = [
            'name' => 'New Name',
            'short_description' => 'New short description',
            'description' => 'New long description',
            'featured' => 0,
            'url' => 'https://example.co.uk',
            'fault_level' => 'most',
            'active_from' => '1970-01-01',
            'active_to' => '2070-01-01',
        ];
        $body = (new Psr17Factory())->createStream(http_build_query($formData));

        $request = $this->createRequest('PATCH', '/api/things/345678')
            ->withHeader('Authorization', 'Basic ' . base64_encode('test:test'))
            ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->withBody($body);
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_NOT_FOUND, $response->getStatusCode());
    }

    public function testInvalidData(): void
    {
        $formData = [
            'name' => 'Thing 1',
            'short_description' => 'Short description',
            'description' => 'Long description',
            'featured' => 1,
            'url' => 'https://example.com',
            'fault_level' => 'all',
            'active_from' => '00000000', // Invalid date format
            'active_to' => '',
        ];
        $body = (new Psr17Factory())->createStream(http_build_query($formData));

        $request = $this->createRequest('PATCH', '/api/things/Uk')
            ->withHeader('Authorization', 'Basic ' . base64_encode('test:test'))
            ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->withBody($body);
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_BAD_REQUEST, $response->getStatusCode());
        $this->assertResponseContains('active_from must be a valid date in the format', $response);
    }

    public function testUnexpectedError(): void
    {
        $mockRepository = $this->createMock(ThingRepository::class);

        $mockValidator = $this->createMock(UpdateThingValidator::class);

        $mockCreator = $this->createMock(ThingUpdater::class);
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
            'name' => 'Thing 1',
            'short_description' => 'Short description',
            'description' => 'Long description',
            'featured' => 1,
            'url' => 'https://example.com',
            'fault_level' => 'all',
            'active_from' => '1970-01-01',
            'active_to' => '',
        ];
        $body = (new Psr17Factory())->createStream(http_build_query($formData));

        $request = (new Psr17Factory())->createServerRequest('PUT', '/api/things/Uk')
            ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->withBody($body);

        $response = (new Psr17Factory())->createResponse();

        $action($request, $response, ['id' => '1']);
    }
}
