<?php

declare(strict_types=1);

namespace App\Test\TestCase\Domain\Thing\Service\Create;

use App\Domain\Thing\Enum\FaultLevel;
use App\Domain\Thing\Repository\ThingRepository;
use App\Domain\Thing\Service\Create\ThingCreator;
use App\Domain\Thing\Thing;
use App\Test\Traits\AppTestTrait;
use App\Test\Traits\DatabaseTestTrait;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[UsesClass(ThingCreator::class)]
class ThingCreatorTest extends TestCase
{
    use AppTestTrait;
    use DatabaseTestTrait;

    public function testDataTransformsSuccessfully(): void
    {
        $data = [
            'name' => 'Test Thing',
            'short_description' => 'Test Thing Short Description',
            'description' => 'Test Thing Long Description',
            'featured' => 1,
            'fault_level' => 'most',
            'active_from' => '1970-01-01',
            'active_to' => '2070-01-01',
            'url' => 'https://example.com/',
        ];

        $repository = new ThingRepository($this->container?->get(Connection::class));
        $creator = new ThingCreator($repository);

        $result = $creator->createFromArray($data);

        $this->assertInstanceOf(Thing::class, $result);
        $this->assertTrue($result->getFeatured());
        $this->assertSame(FaultLevel::MOSTLY, $result->getFaultLevel());
        $this->assertSame(-3600, $result->getActiveFrom());
        $this->assertSame(3155760000, $result->getActiveTo());
    }

    public function testAlternativeDataTransformsSuccessfully(): void
    {
        $data = [
            'name' => 'Test Thing',
            'short_description' => 'Test Thing Short Description',
            'description' => 'Test Thing Long Description',
            'featured' => '',
            'fault_level' => 'all',
            'active_from' => '1970-01-01',
            'active_to' => '',
            'url' => '',
        ];

        $repository = new ThingRepository($this->container?->get(Connection::class));
        $creator = new ThingCreator($repository);

        $result = $creator->createFromArray($data);

        $this->assertInstanceOf(Thing::class, $result);
        $this->assertFalse($result->getFeatured());
        $this->assertSame(FaultLevel::ALL, $result->getFaultLevel());
        $this->assertNull($result->getActiveTo());
        $this->assertNull($result->getUrl());
    }
}
