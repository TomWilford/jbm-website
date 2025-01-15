<?php

declare(strict_types=1);

namespace App\Test\TestCase\Domain\Thing;

use App\Domain\Thing\Thing;
use App\Domain\Thing\Enum\FaultLevel;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[UsesClass(Thing::class)]
class ThingTest extends TestCase
{
    public function testCanInstantiateThing(): void
    {
        $thing = new Thing(
            id: 1,
            name: 'Test Thing',
            shortDescription: 'Short description',
            description: 'Detailed description',
            featured: true,
            faultLevel: FaultLevel::ALL,
            activeFrom: 1620000000,
            activeTo: 1625000000,
            url: 'https://example.com',
            createdAt: 1600000000,
            updatedAt: 1601000000
        );

        $this->assertSame(1, $thing->getId());
        $this->assertSame('Test Thing', $thing->getName());
        $this->assertSame('Short description', $thing->getShortDescription());
        $this->assertSame('Detailed description', $thing->getDescription());
        $this->assertTrue($thing->getFeatured());
        $this->assertSame(FaultLevel::ALL, $thing->getFaultLevel());
        $this->assertSame(1620000000, $thing->getActiveFrom());
        $this->assertSame(1625000000, $thing->getActiveTo());
        $this->assertSame('https://example.com', $thing->getUrl());
        $this->assertSame('example.com', $thing->getUrlHost());
        $this->assertSame(1600000000, $thing->getCreatedAt());
        $this->assertSame(1601000000, $thing->getUpdatedAt());
    }

    public function testJsonSerialization(): void
    {
        $thing = new Thing(
            id: 1,
            name: 'Test Thing',
            shortDescription: 'Short description',
            description: 'Detailed description',
            featured: true,
            faultLevel: FaultLevel::ALL,
            activeFrom: 1620000000,
            activeTo: 1625000000,
            url: 'https://example.com',
            createdAt: 1600000000,
            updatedAt: 1601000000
        );

        $expected = [
            'id' => 1,
            'name' => 'Test Thing',
            'short_description' => 'Short description',
            'description' => 'Detailed description',
            'featured' => true,
            'url' => 'https://example.com',
            'fault_level' => FaultLevel::ALL,
            'active_from' => 1620000000,
            'active_to' => 1625000000,
            'created_at' => 1600000000,
            'updated_at' => 1601000000,
        ];

        $this->assertSame($expected, $thing->jsonSerialize());
    }

    public function testCloneWith(): void
    {
        $thing = new Thing(
            id: 1,
            name: 'Test Thing',
            shortDescription: 'Short description',
            description: 'Detailed description',
            featured: true,
            faultLevel: FaultLevel::ALL,
            activeFrom: 1620000000,
            activeTo: 1625000000,
            url: 'https://example.com',
            createdAt: 1600000000,
            updatedAt: 1601000000
        );

        $clonedThing = $thing->cloneWith(name: 'Updated Thing', featured: false);

        $this->assertSame('Updated Thing', $clonedThing->getName());
        $this->assertFalse($clonedThing->getFeatured());
        $this->assertSame($thing->getId(), $clonedThing->getId());
        $this->assertSame($thing->getFaultLevel(), $clonedThing->getFaultLevel());
        $this->assertSame($thing->getActiveFrom(), $clonedThing->getActiveFrom());
        $this->assertSame($thing->getActiveTo(), $clonedThing->getActiveTo());
        $this->assertSame($thing->getUrl(), $clonedThing->getUrl());
    }

    public function testCloneWithUnchangedValues(): void
    {
        $thing = new Thing(
            id: 1,
            name: 'Test Thing',
            shortDescription: 'Short description',
            description: 'Detailed description',
            featured: true,
            faultLevel: FaultLevel::ALL,
            activeFrom: 1620000000,
            activeTo: 1625000000,
            url: 'https://example.com',
            createdAt: 1600000000,
            updatedAt: 1601000000
        );

        $clonedThing = $thing->cloneWith();

        $this->assertSame($thing->getName(), $clonedThing->getName());
        $this->assertSame($thing->getFeatured(), $clonedThing->getFeatured());
        $this->assertSame($thing->getId(), $clonedThing->getId());
        $this->assertSame($thing->getFaultLevel(), $clonedThing->getFaultLevel());
        $this->assertSame($thing->getActiveFrom(), $clonedThing->getActiveFrom());
        $this->assertSame($thing->getActiveTo(), $clonedThing->getActiveTo());
        $this->assertSame($thing->getUrl(), $clonedThing->getUrl());
    }

    public function testGetUrlHostHandlesNull(): void
    {
        $thing = new Thing(
            id: null,
            name: 'No URL',
            shortDescription: '',
            description: '',
            featured: false,
            faultLevel: FaultLevel::ALL,
            activeFrom: 1620000000
        );

        $this->assertNull($thing->getUrlHost());
    }

    public function testGetUrlHostHandlesInvalidUrl(): void
    {
        $thing = new Thing(
            id: null,
            name: 'Invalid URL',
            shortDescription: '',
            description: '',
            featured: false,
            faultLevel: FaultLevel::ALL,
            activeFrom: 1620000000,
            url: 'not-a-valid-url'
        );

        $this->assertNull($thing->getUrlHost());
    }
}
