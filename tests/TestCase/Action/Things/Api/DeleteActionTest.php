<?php

declare(strict_types=1);

namespace App\Test\TestCase\Action\Things\Api;

use App\Action\Things\Api\DeleteAction;
use App\Domain\Exception\DomainRecordNotFoundException;
use App\Domain\Thing\Enum\FaultLevel;
use App\Domain\Thing\Repository\ThingRepository;
use App\Domain\Thing\Thing;
use App\Test\Traits\AppTestTrait;
use App\Test\Traits\DatabaseTestTrait;
use Doctrine\DBAL\Connection;
use Fig\Http\Message\StatusCodeInterface;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[UsesClass(DeleteAction::class)]
class DeleteActionTest extends TestCase
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

        $request = $this->createRequest('DELETE', '/api/things/' . $thing->getId())
            ->withHeader('Authorization', 'Basic ' . base64_encode('test:test'));
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        $this->assertResponseContains('Thing deleted successfully', $response);

        $this->expectException(DomainRecordNotFoundException::class);
        $repository->ofId($thing->getId());
    }

    public function testDeleteUnknownId(): void
    {
        $request = $this->createRequest('DELETE', '/api/things/bontrager')
            ->withHeader('Authorization', 'Basic ' . base64_encode('test:test'));
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_NOT_FOUND, $response->getStatusCode());
    }
}
