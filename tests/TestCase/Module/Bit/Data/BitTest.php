<?php

declare(strict_types=1);

namespace App\Test\TestCase\Module\Bit\Data;

use App\Module\Bit\Data\Bit;
use App\Module\Bit\Enum\Language;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Sqids\Sqids;
use TomWilford\SlimSqids\GlobalSqidConfiguration;

#[UsesClass(Bit::class)]
class BitTest extends TestCase
{
    protected function setUp(): void
    {
        try {
            GlobalSqidConfiguration::get();
        } catch (\RuntimeException $exception) {
            GlobalSqidConfiguration::set(new Sqids());
        }
    }

    public function testCanInstantiateThing(): void
    {
        $bit = new Bit(
            id: 1,
            name: 'Test Bit',
            code: "var_dump(sprintf('%s %s!', 'Hello', 'World'));",
            language: Language::PHP,
            description: 'Test description',
            returns: 'string(12) "Hello World!"',
            createdAt: 1600000000,
            updatedAt: 1601000000
        );

        $this->assertSame(1, $bit->getId());
        $this->assertSame('Uk', $bit->getSqid());
        $this->assertSame('Test Bit', $bit->getName());
        $this->assertSame("var_dump(sprintf('%s %s!', 'Hello', 'World'));", $bit->getCode());
        $this->assertSame(Language::PHP, $bit->getLanguage());
        $this->assertSame('Test description', $bit->getDescription());
        $this->assertSame('string(12) "Hello World!"', $bit->getReturns());
        $this->assertSame(1600000000, $bit->getCreatedAt());
        $this->assertSame(1601000000, $bit->getUpdatedAt());
    }

    public function testJsonSerialization(): void
    {
        $bit = new Bit(
            id: 1,
            name: 'Test Bit',
            code: "var_dump(sprintf('%s %s!', 'Hello', 'World'));",
            language: Language::PHP,
            description: 'Test description',
            returns: 'string(12) "Hello World!"',
            createdAt: 1600000000,
            updatedAt: 1601000000
        );

        $expected = [
            'id' => 'Uk',
            'name' => 'Test Bit',
            'code' => "var_dump(sprintf('%s %s!', 'Hello', 'World'));",
            'language' => Language::PHP->name,
            'description' => 'Test description',
            'returns' => 'string(12) "Hello World!"',
            'created_at' => 1600000000,
            'updated_at' => 1601000000,
        ];

        $this->assertSame($expected, $bit->jsonSerialize());
    }

    public function testCloneWith(): void
    {
        $bit = new Bit(
            id: 1,
            name: 'Test Bit',
            code: "var_dump(sprintf('%s %s!', 'Hello', 'World'));",
            language: Language::PHP,
            description: 'Test description',
            returns: 'string(12) "Hello World!"',
            createdAt: 1600000000,
            updatedAt: 1601000000
        );

        $clonedBit = $bit->cloneWith(name: 'Updated bit', description: '');

        $this->assertSame('Updated bit', $clonedBit->getName());
        $this->assertSame('', $clonedBit->getDescription());
        $this->assertSame($bit->getId(), $clonedBit->getId());
        $this->assertSame($bit->getCode(), $clonedBit->getCode());
    }

    public function testCloneWithUnchangedValues(): void
    {
        $bit = new Bit(
            id: 1,
            name: 'Test Bit',
            code: "var_dump(sprintf('%s %s!', 'Hello', 'World'));",
            language: Language::PHP,
            description: 'Test description',
            returns: 'string(12) "Hello World!"',
            createdAt: 1600000000,
            updatedAt: 1601000000
        );

        $clonedBit = $bit->cloneWith();

        $this->assertSame(1, $clonedBit->getId());
        $this->assertSame('Test Bit', $clonedBit->getName());
        $this->assertSame("var_dump(sprintf('%s %s!', 'Hello', 'World'));", $clonedBit->getCode());
        $this->assertSame(Language::PHP, $clonedBit->getLanguage());
        $this->assertSame('Test description', $clonedBit->getDescription());
        $this->assertSame('string(12) "Hello World!"', $clonedBit->getReturns());
        $this->assertSame(1600000000, $clonedBit->getCreatedAt());
        $this->assertSame(1601000000, $clonedBit->getUpdatedAt());
    }
}
