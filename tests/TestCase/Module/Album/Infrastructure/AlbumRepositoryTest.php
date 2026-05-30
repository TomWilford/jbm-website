<?php

declare(strict_types=1);

namespace App\Test\TestCase\Module\Album\Infrastructure;

use App\Infrastructure\Exception\DomainRecordNotFoundException;
use App\Module\Album\Domain\Album;
use App\Module\Album\Domain\Camera;
use App\Module\Album\Infrastructure\AlbumRepository;
use App\Test\Traits\AppTestTrait;
use App\Test\Traits\DatabaseTestTrait;
use Doctrine\DBAL\Connection;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(AlbumRepository::class)]
class AlbumRepositoryTest extends TestCase
{
    use AppTestTrait;
    use DatabaseTestTrait;

    public function testOfId(): void
    {
        $repository = new AlbumRepository($this->container?->get(Connection::class));
        $result = $repository->ofId(1);

        $this->assertInstanceOf(Album::class, $result);
        $this->assertSame(1, $result->getId());
    }

    public function testStore(): void
    {
        $album = new Album(
            id: null,
            name: 'Summer Holiday',
            camera: Camera::OLYMPUS_PEN,
            location: 'Greece',
            date: '2023-08-15'
        );

        $repository = new AlbumRepository($this->container?->get(Connection::class));
        $result = $repository->store($album);

        $this->assertInstanceOf(Album::class, $result);
        $this->assertSame(100, $result->getId());
        $this->assertSame('Summer Holiday', $result->getName());
        $this->assertSame(Camera::OLYMPUS_PEN, $result->getCamera());
    }

    public function testAll(): void
    {
        $repository = new AlbumRepository($this->container?->get(Connection::class));
        $result = $repository->all();

        $this->assertIsIterable($result);
        $this->assertInstanceOf(Album::class, $result[0]);
        $this->assertSame(1, $result[0]->getId());
    }

    public function testUpdate(): void
    {
        $album = new Album(
            id: null,
            name: 'Original Name',
            camera: Camera::OLYMPUS_PEN,
            location: 'Original Location',
            date: '2023-01-01'
        );

        $repository = new AlbumRepository($this->container?->get(Connection::class));
        $newAlbum = $repository->store($album);

        $updatedAlbum = $newAlbum->cloneWith(
            name: 'Updated Album Name',
            location: 'New York'
        );

        $result = $repository->update($updatedAlbum);

        $this->assertInstanceOf(Album::class, $result);
        $this->assertSame($updatedAlbum->getId(), $result->getId());
        $this->assertSame('Updated Album Name', $result->getName());
        $this->assertSame('New York', $result->getLocation());
    }

    public function testDestroy(): void
    {
        $album = new Album(
            id: null,
            name: 'To Be Deleted',
            camera: Camera::OLYMPUS_PEN,
            location: 'Wastebasket',
            date: '2023-01-01'
        );

        $repository = new AlbumRepository($this->container?->get(Connection::class));
        $newAlbum = $repository->store($album);

        $this->assertInstanceOf(Album::class, $newAlbum);

        $repository->destroy($newAlbum);

        $this->expectException(DomainRecordNotFoundException::class);
        $repository->ofId($newAlbum->getId());
    }

    public function testDestroyNonexistentAlbum(): void
    {
        $album = new Album(
            id: 9999999,
            name: 'Ghost Album',
            camera: Camera::OLYMPUS_PEN,
            location: 'Nowhere',
            date: '2023-01-01'
        );
        $repository = new AlbumRepository($this->container?->get(Connection::class));

        $this->expectException(DomainRecordNotFoundException::class);
        $repository->destroy($album);
    }

    public function testStoreThrowsExceptionWhenWrongEntityProvided(): void
    {
        $invalidClass = new stdClass();
        $repository = new AlbumRepository($this->container?->get(Connection::class));

        $this->expectException(InvalidArgumentException::class);
        $repository->store($invalidClass);
    }

    public function testUpdateThrowsExceptionWhenWrongEntityProvided(): void
    {
        $invalidClass = new stdClass();
        $repository = new AlbumRepository($this->container?->get(Connection::class));

        $this->expectException(InvalidArgumentException::class);
        $repository->update($invalidClass);
    }

    public function testDestroyThrowsExceptionWhenWrongEntityProvided(): void
    {
        $invalidClass = new stdClass();
        $repository = new AlbumRepository($this->container?->get(Connection::class));

        $this->expectException(InvalidArgumentException::class);
        $repository->destroy($invalidClass);
    }
}
