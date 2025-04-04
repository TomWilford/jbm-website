<?php

declare(strict_types=1);

namespace App\Test\TestCase\Module\Bit\Update\Domain;

use App\Module\Bit\Data\Bit;
use App\Module\Bit\Enum\Language;
use App\Module\Bit\Infrastructure\BitRepository;
use App\Module\Bit\Update\Domain\BitUpdater;
use App\Test\Traits\AppTestTrait;
use App\Test\Traits\DatabaseTestTrait;
use Doctrine\DBAL\Connection;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use stdClass;

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
            'Initial Description',
            'Initial Returns'
        );

        $bit = $repository->store($bit);

        $data = [
            'name' => 'New Name',
            'code' => 'New Code',
            'language' => 'MIXED',
            'description' => 'New Description',
            'returns' => 'New Returns',
        ];

        $bitUpdater = new BitUpdater($repository);

        $result = $bitUpdater->updateFromArray($data, $bit);

        $this->assertInstanceOf(Bit::class, $result);
        $this->assertSame('New Name', $result->getName());
        $this->assertSame('New Code', $result->getCode());
        $this->assertSame(Language::MIXED, $result->getLanguage());
        $this->assertSame('New Description', $result->getDescription());
        $this->assertSame('New Returns', $result->getReturns());
    }

    public function testUpdateAllFieldsWhenBlankFromArray(): void
    {
        $repository = new BitRepository($this->container?->get(Connection::class));

        $bit = new Bit(
            null,
            'Initial Name',
            'Initial Code',
            Language::PHP,
            'Initial Description',
            'Initial Returns'
        );

        $bit = $repository->store($bit);

        $data = [
            'name' => '',
            'code' => '',
            'language' => '',
            'description' => '',
            'returns' => '',
        ];

        $bitUpdater = new BitUpdater($repository);

        $result = $bitUpdater->updateFromArray($data, $bit);

        $this->assertInstanceOf(Bit::class, $result);
        $this->assertSame('Initial Name', $result->getName());
        $this->assertSame('Initial Code', $result->getCode());
        $this->assertSame(Language::PHP, $result->getLanguage());
        $this->assertSame('Initial Description', $result->getDescription());
        $this->assertSame('Initial Returns', $result->getReturns());
    }

    public function testUpdateWithNullableValues(): void
    {
        $repository = new BitRepository($this->container?->get(Connection::class));

        $bit = new Bit(
            null,
            'Initial Name',
            'Initial Code',
            Language::PHP,
            'Initial Description',
            'Initial Returns'
        );

        $bit = $repository->store($bit);

        $data = [
            'name' => 'New Name',
            'code' => 'New Code',
            'language' => 'MIXED',
            'description' => 'null',
            'returns' => 'null',
        ];

        $bitUpdater = new BitUpdater($repository);

        $result = $bitUpdater->updateFromArray($data, $bit);

        $this->assertInstanceOf(Bit::class, $result);
        $this->assertSame('New Name', $result->getName());
        $this->assertSame('New Code', $result->getCode());
        $this->assertSame(Language::MIXED, $result->getLanguage());
        $this->assertNull($result->getDescription());
        $this->assertNull($result->getReturns());
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
            'returns' => 'null',
        ];

        $invalidClass = new stdClass();

        $this->expectException(InvalidArgumentException::class);
        $result = $bitUpdater->updateFromArray($data, $invalidClass);
    }
}
