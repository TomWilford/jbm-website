<?php

declare(strict_types=1);

namespace App\Test\TestCase\Domain\Bit\Service\Update;

use App\Domain\Bit\Bit;
use App\Domain\Bit\Enum\Language;
use App\Domain\Bit\Repository\BitRepository;
use App\Domain\Bit\Service\Update\BitUpdater;
use App\Test\Traits\AppTestTrait;
use App\Test\Traits\DatabaseTestTrait;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[UsesClass(BitUpdater::class)]
class BitUpdaterTest extends TestCase
{
    use AppTestTrait;
    use DatabaseTestTrait;

    public function testUpdateAllFieldsFromArray(): void
    {
        $repository = new BitRepository($this->container?->get(Connection::class));

        $bit = new Bit(
            null,
            'Initial Name',
            'Initial Code',
            Language::PHP,
            'Initial Description'
        );

        $bit = $repository->store($bit);

        $data = [
            'name' => 'New Name',
            'code' => 'New Code',
            'language' => 'MIXED',
            'description' => 'New Description',
        ];

        $bitUpdater = new BitUpdater($repository);

        $result = $bitUpdater->updateFromArray($data, $bit);

        $this->assertInstanceOf(Bit::class, $result);
        $this->assertSame('New Name', $result->getName());
        $this->assertSame('New Code', $result->getCode());
        $this->assertSame(Language::MIXED, $result->getLanguage());
        $this->assertSame('New Description', $result->getDescription());
    }

    public function testUpdateAllFieldsWhenBlankFromArray(): void
    {
        $repository = new BitRepository($this->container?->get(Connection::class));

        $bit = new Bit(
            null,
            'Initial Name',
            'Initial Code',
            Language::PHP,
            'Initial Description'
        );

        $bit = $repository->store($bit);

        $data = [
            'name' => '',
            'code' => '',
            'language' => '',
            'description' => '',
        ];

        $bitUpdater = new BitUpdater($repository);

        $result = $bitUpdater->updateFromArray($data, $bit);

        $this->assertInstanceOf(Bit::class, $result);
        $this->assertSame('Initial Name', $result->getName());
        $this->assertSame('Initial Code', $result->getCode());
        $this->assertSame(Language::PHP, $result->getLanguage());
        $this->assertSame('Initial Description', $result->getDescription());
    }

    public function testUpdateWithNullableValues(): void
    {
        $repository = new BitRepository($this->container?->get(Connection::class));

        $bit = new Bit(
            null,
            'Initial Name',
            'Initial Code',
            Language::PHP,
            'Initial Description'
        );

        $bit = $repository->store($bit);

        $data = [
            'name' => 'New Name',
            'code' => 'New Code',
            'language' => 'MIXED',
            'description' => 'null',
        ];

        $bitUpdater = new BitUpdater($repository);

        $result = $bitUpdater->updateFromArray($data, $bit);

        $this->assertInstanceOf(Bit::class, $result);
        $this->assertSame('New Name', $result->getName());
        $this->assertSame('New Code', $result->getCode());
        $this->assertSame(Language::MIXED, $result->getLanguage());
        $this->assertNull($result->getDescription());
    }

    public function testWrongEntityPassedToUpdater(): void
    {
        $repository = new BitRepository($this->container?->get(Connection::class));
        $bitUpdater = new BitUpdater($repository);

        $data = [
            'name' => 'New Name',
            'code' => 'New Code',
            'language' => 'MIXED',
            'description' => 'null',
        ];

        $invalidClass = new \stdClass();

        $this->expectException(\InvalidArgumentException::class);
        $result = $bitUpdater->updateFromArray($data, $invalidClass);
    }
}
