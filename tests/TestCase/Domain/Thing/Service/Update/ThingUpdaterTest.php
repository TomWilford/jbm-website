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
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[UsesClass(ThingUpdater::class)]
class ThingUpdaterTest extends TestCase
{
    use AppTestTrait;
    use DatabaseTestTrait;

    public function testUpdateAllFieldsFromArray()
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
        $this->assertEquals('New Name', $result->getName());
        $this->assertEquals('New Short Description', $result->getShortDescription());
        $this->assertEquals('New Description', $result->getDescription());
        $this->assertFalse($result->getFeatured());
        $this->assertEquals(FaultLevel::MOSTLY, $result->getFaultLevel());
        $this->assertEquals(1735689600, $result->getActiveFrom());
        $this->assertEquals(1767139200, $result->getActiveTo());
        $this->assertEquals('https://example.co.uk', $result->getUrl());
    }

    public function testUpdateAllFieldsWhenBlankFromArray()
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
        $this->assertEquals('Initial Name', $result->getName());
        $this->assertEquals('Initial Short Description', $result->getShortDescription());
        $this->assertEquals('Initial Description', $result->getDescription());
        $this->assertTrue($result->getFeatured());
        $this->assertEquals(FaultLevel::ALL, $result->getFaultLevel());
        $this->assertEquals(-3600, $result->getActiveFrom());
        $this->assertEquals(3155760000, $result->getActiveTo());
        $this->assertEquals('https://example.com', $result->getUrl());
    }

    public function testUpdateWithNullableValues()
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
}
