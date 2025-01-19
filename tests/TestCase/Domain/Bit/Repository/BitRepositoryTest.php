<?php

declare(strict_types=1);

namespace App\Test\TestCase\Domain\Bit\Repository;

use App\Domain\Bit\Bit;
use App\Domain\Bit\Enum\Language;
use App\Domain\Bit\Repository\BitRepository;
use App\Domain\Exception\DomainRecordNotFoundException;
use App\Test\Traits\AppTestTrait;
use App\Test\Traits\DatabaseTestTrait;
use Doctrine\DBAL\Connection;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use stdClass;

#[UsesClass(BitRepository::class)]
class BitRepositoryTest extends TestCase
{
    use AppTestTrait;
    use DatabaseTestTrait;

    public function testOfId(): void
    {
        $repository = new BitRepository($this->container?->get(Connection::class));
        $result = $repository->ofId(1);

        $this->assertInstanceOf(Bit::class, $result);
        $this->assertSame(1, $result->getId());
    }

    public function testStore(): void
    {
        $bit = new Bit(
            id: null,
            name: 'Test Bit',
            code: "var_dump(sprintf('%s %s!', 'Hello', 'World'));",
            language: Language::PHP,
            description: 'Test description',
            createdAt: 1600000000,
            updatedAt: 1601000000
        );

        $repository = new BitRepository($this->container?->get(Connection::class));
        $result = $repository->store($bit);

        $this->assertInstanceOf(Bit::class, $result);
        $this->assertSame(100, $result->getId());
    }

    public function testAll(): void
    {
        $repository = new BitRepository($this->container?->get(Connection::class));
        $result = $repository->all();

        $this->assertIsIterable($result);
        $this->assertInstanceOf(Bit::class, $result[0]);
        $this->assertSame(1, $result[0]->getId());
    }

    public function testUpdate(): void
    {
        $bit = new Bit(
            id: null,
            name: 'Test Bit',
            code: "var_dump(sprintf('%s %s!', 'Hello', 'World'));",
            language: Language::PHP,
            description: 'Test description',
            createdAt: 1600000000,
            updatedAt: 1601000000
        );

        $repository = new BitRepository($this->container?->get(Connection::class));
        $newBit = $repository->store($bit);

        $updatedBit = $newBit->cloneWith(name: 'Updated Test Bit');
        $result = $repository->update($updatedBit);

        $this->assertInstanceOf(Bit::class, $result);
        $this->assertSame($updatedBit->getId(), $result->getId());
        $this->assertSame('Updated Test Bit', $result->getName());
    }

    public function testDestroy(): void
    {
        $bit = new Bit(
            id: null,
            name: 'Test Bit',
            code: "var_dump(sprintf('%s %s!', 'Hello', 'World'));",
            language: Language::PHP,
            description: 'Test description',
            createdAt: 1600000000,
            updatedAt: 1601000000
        );

        $repository = new BitRepository($this->container?->get(Connection::class));
        $newBit = $repository->store($bit);

        $this->assertInstanceOf(Bit::class, $newBit);

        $repository->destroy($newBit);

        $this->expectException(DomainRecordNotFoundException::class);
        $repository->ofId($newBit->getId());
    }

    public function testDestroyNonExistentThing(): void
    {
        $bit = new Bit(
            id: 999999999999,
            name: 'Test Bit',
            code: "var_dump(sprintf('%s %s!', 'Hello', 'World'));",
            language: Language::PHP,
            description: 'Test description',
            createdAt: 1600000000,
            updatedAt: 1601000000
        );

        $repository = new BitRepository($this->container?->get(Connection::class));

        $this->expectException(DomainRecordNotFoundException::class);
        $repository->ofId($bit->getId());
    }

    public function testStoreThrowsExceptionWhenWrongEntityProvided(): void
    {
        $invalidClass = new stdClass();
        $repository = new BitRepository($this->container?->get(Connection::class));
        $this->expectException(InvalidArgumentException::class);
        $repository->store($invalidClass);
    }

    public function testUpdateThrowsExceptionWhenWrongEntityProvided(): void
    {
        $invalidClass = new stdClass();
        $repository = new BitRepository($this->container?->get(Connection::class));
        $this->expectException(InvalidArgumentException::class);
        $repository->update($invalidClass);
    }

    public function testDestroyThrowsExceptionWhenWrongEntityProvided(): void
    {
        $invalidClass = new stdClass();
        $repository = new BitRepository($this->container?->get(Connection::class));
        $this->expectException(InvalidArgumentException::class);
        $repository->destroy($invalidClass);
    }

    public function testUpdateThrowsExceptionWhenEntityWithNullIdProvided(): void
    {
        $bit = new Bit(
            id: null,
            name: 'Test Bit',
            code: "var_dump(sprintf('%s %s!', 'Hello', 'World'));",
            language: Language::PHP,
            description: 'Test description',
            createdAt: 1600000000,
            updatedAt: 1601000000
        );
        $repository = new BitRepository($this->container?->get(Connection::class));
        $this->expectException(DomainRecordNotFoundException::class);
        $repository->update($bit);
    }
}
