<?php

declare(strict_types=1);

namespace App\Test\TestCase\Domain\Thing\Service\Update;

use App\Domain\Thing\Enum\FaultLevel;
use App\Domain\Thing\Repository\ThingRepository;
use App\Domain\Thing\Service\Update\ThingUpdater;
use App\Domain\Thing\Thing;
use App\Test\Traits\AppTestTrait;
use App\Test\Traits\DatabaseTestTrait;
use Doctrine\DBAL\Connection;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use stdClass;

#[UsesClass(ThingUpdater::class)]
class ThingUpdaterTest extends TestCase
{
    use AppTestTrait;
    use DatabaseTestTrait;

    public function testUpdateAllFieldsFromArray(): void
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

        $data = [
            'name' => 'New Name',
            'short_description' => 'New Short Description',
            'description' => 'New Description',
            'featured' => false,
            'fault_level' => FaultLevel::MOSTLY->value,
            'active_from' => '2025-01-01',
            'active_to' => '2025-12-31',
            'url' => 'https://example.co.uk',
        ];

        $thingUpdater = new ThingUpdater($repository);

        $result = $thingUpdater->updateFromArray($data, $thing);

        $this->assertInstanceOf(Thing::class, $result);
        $this->assertSame('New Name', $result->getName());
        $this->assertSame('New Short Description', $result->getShortDescription());
        $this->assertSame('New Description', $result->getDescription());
        $this->assertFalse($result->getFeatured());
        $this->assertSame(FaultLevel::MOSTLY, $result->getFaultLevel());
        $this->assertSame(1735689600, $result->getActiveFrom());
        $this->assertSame(1767139200, $result->getActiveTo());
        $this->assertSame('https://example.co.uk', $result->getUrl());
    }

    public function testUpdateAllFieldsWhenBlankFromArray(): void
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

        $data = [
            'name' => '',
            'short_description' => '',
            'description' => '',
            'featured' => '',
            'fault_level' => '',
            'active_from' => '',
            'active_to' => '',
            'url' => '',
        ];

        $thingUpdater = new ThingUpdater($repository);

        $result = $thingUpdater->updateFromArray($data, $thing);

        $this->assertInstanceOf(Thing::class, $result);
        $this->assertSame('Initial Name', $result->getName());
        $this->assertSame('Initial Short Description', $result->getShortDescription());
        $this->assertSame('Initial Description', $result->getDescription());
        $this->assertTrue($result->getFeatured());
        $this->assertSame(FaultLevel::ALL, $result->getFaultLevel());
        $this->assertSame(-3600, $result->getActiveFrom());
        $this->assertSame(3155760000, $result->getActiveTo());
        $this->assertSame('https://example.com', $result->getUrl());
    }

    public function testUpdateWithNullableValues(): void
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

        $data = [
            'name' => '',
            'short_description' => '',
            'description' => '',
            'featured' => '',
            'fault_level' => '',
            'active_from' => '',
            'active_to' => 'null',
            'url' => 'null',
        ];

        $thingUpdater = new ThingUpdater($repository);

        $result = $thingUpdater->updateFromArray($data, $thing);

        $this->assertInstanceOf(Thing::class, $result);

        $this->assertNull($result->getActiveTo());
        $this->assertNull($result->getUrl());
    }

    public function testWrongEntityPassedToUpdater(): void
    {
        $repository = new ThingRepository($this->container?->get(Connection::class));
        $bitUpdater = new ThingUpdater($repository);

        $data = [
            'name' => '',
            'short_description' => '',
            'description' => '',
            'featured' => '',
            'fault_level' => '',
            'active_from' => '',
            'active_to' => 'null',
            'url' => 'null',
        ];

        $invalidClass = new stdClass();

        $this->expectException(InvalidArgumentException::class);
        $result = $bitUpdater->updateFromArray($data, $invalidClass);
    }
}
