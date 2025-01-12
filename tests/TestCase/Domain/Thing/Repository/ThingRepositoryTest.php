<?php

declare(strict_types=1);

namespace App\Test\TestCase\Domain\Thing\Repository;

use App\Domain\Exception\DomainRecordNotFoundException;
use App\Domain\Thing\Enum\FaultLevel;
use App\Domain\Thing\Repository\ThingRepository;
use App\Domain\Thing\Thing;
use App\Test\Traits\AppTestTrait;
use App\Test\Traits\DatabaseTestTrait;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[UsesClass(ThingRepository::class)]
class ThingRepositoryTest extends TestCase
{
    use AppTestTrait;
    use DatabaseTestTrait;

    public function testOfId(): void
    {
        $repository = new ThingRepository($this->container?->get(Connection::class));
        $result = $repository->ofId(1);
        $this->assertInstanceOf(Thing::class, $result);
        $this->assertEquals(1, $result->getId());
    }

    public function testStore(): void
    {
        $thing = new Thing(
            id: null,
            name: 'Test 3',
            shortDescription: 'Short description 3',
            description: 'Long description 3',
            featured: false,
            faultLevel: FaultLevel::ALL,
            activeFrom: (new \DateTimeImmutable('2013-05-18'))->getTimestamp(),
            activeTo: null,
            url: 'https://example.com/',
        );
        $repository = new ThingRepository($this->container?->get(Connection::class));
        $result = $repository->store($thing);

        $this->assertInstanceOf(Thing::class, $result);
        $this->assertEquals(100, $result->getId());
    }

    public function testFindAll(): void
    {
        $repository = new ThingRepository($this->container?->get(Connection::class));
        $result = $repository->all();
        $this->assertIsArray($result);
        $this->assertInstanceOf(Thing::class, $result[0]);
        $this->assertEquals(1, $result[0]->getId());
    }

    public function testUpdate(): void
    {
        $thing = new Thing(
            id: null,
            name: 'Test 3',
            shortDescription: 'Short description 3',
            description: 'Long description 3',
            featured: false,
            faultLevel: FaultLevel::ALL,
            activeFrom: (new \DateTimeImmutable('2013-05-18'))->getTimestamp(),
            activeTo: null,
            url: 'https://example.com/',
        );
        $repository = new ThingRepository($this->container?->get(Connection::class));
        $newThing = $repository->store($thing);

        $updatedThing = $newThing->cloneWith(name: 'Updated Test');
        $repository->update($updatedThing);

        $result = $repository->ofId($updatedThing->getId());

        $this->assertInstanceOf(Thing::class, $result);
        $this->assertEquals($updatedThing->getId(), $result->getId());
        $this->assertEquals('Updated Test', $result->getName());
    }

    public function testDestroy(): void
    {
        $thing = new Thing(
            id: null,
            name: 'Test 3',
            shortDescription: 'Short description 3',
            description: 'Long description 3',
            featured: false,
            faultLevel: FaultLevel::ALL,
            activeFrom: (new \DateTimeImmutable('2013-05-18'))->getTimestamp(),
            activeTo: null,
            url: 'https://example.com/',
        );
        $repository = new ThingRepository($this->container?->get(Connection::class));
        $newThing = $repository->store($thing);

        $this->assertInstanceOf(Thing::class, $newThing);

        $repository->destroy($newThing);

        $this->expectException(DomainRecordNotFoundException::class);
        $repository->ofId($newThing->getId());
    }

    public function testRecent(): void
    {
        $repository = new ThingRepository($this->container?->get(Connection::class));
        $result = $repository->recent();
        $this->assertIsArray($result);
        $this->assertInstanceOf(Thing::class, $result[0]);
        $this->assertEquals(1, $result[0]->getId());
    }
}