<?php

declare(strict_types=1);

namespace App\Test\TestCase\Action\Things\Api;

use App\Action\Things\Api\CreateAction;
use App\Test\Traits\AppTestTrait;
use App\Test\Traits\DatabaseTestTrait;
use Fig\Http\Message\StatusCodeInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[UsesClass(CreateAction::class)]
class CreateActionTest extends TestCase
{
    use AppTestTrait;
    use DatabaseTestTrait;

    public function testAction(): void
    {
        $formData = [
            'name' => 'Thing 1',
            'short_description' => 'Short description',
            'description' => 'Long description',
            'featured' => 1,
            'url' => 'https://example.com',
            'fault_level' => 'all',
            'active_from' => '1970-01-01',
            'active_to' => ''
        ];
        $body = (new Psr17Factory())->createStream(http_build_query($formData));

        $request = $this->createRequest('POST', '/api/things')
            ->withHeader('Authorization', 'Basic ' . base64_encode('test:test'))
            ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->withBody($body);
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        $this->assertResponseContains('Thing 1', $response);
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
            'active_to' => ''
        ];
        $body = (new Psr17Factory())->createStream(http_build_query($formData));

        $request = $this->createRequest('POST', '/api/things')
            ->withHeader('Authorization', 'Basic ' . base64_encode('test:test'))
            ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->withBody($body);
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_BAD_REQUEST, $response->getStatusCode());
        $this->assertResponseContains('active_from must be a valid date in the format', $response);
    }
}
