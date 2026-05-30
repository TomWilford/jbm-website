<?php

declare(strict_types=1);

namespace App\Test\TestCase\Module\Snap\Infrastructure;

use App\Infrastructure\Exception\DomainRecordNotFoundException;
use App\Module\Snap\Domain\Orientation;
use App\Module\Snap\Domain\Snap;
use App\Module\Snap\Infrastructure\SnapRepository;
use App\Test\Traits\AppTestTrait;
use App\Test\Traits\DatabaseTestTrait;
use Doctrine\DBAL\Connection;
use InvalidArgumentException;
use Nyholm\Psr7\Stream;
use PhpCommonEnums\MimeType\Enumeration\MimeTypeEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use stdClass;

#[CoversClass(SnapRepository::class)]
class SnapRepositoryTest extends TestCase
{
    use AppTestTrait;
    use DatabaseTestTrait;

    private StreamInterface $exampleImage;

    protected function setUp(): void
    {
        $this->exampleImage = new Stream(
            fopen(dirname(__DIR__, 4) . '/Fixtures/assets/snap-01.webp', 'r')
        );
        $this->setUpApp();
    }

    public function testOfId(): void
    {
        $repository = new SnapRepository($this->container?->get(Connection::class));
        $result = $repository->ofId(1);

        $this->assertInstanceOf(Snap::class, $result);
        $this->assertSame(1, $result->getId());
    }

    public function testStore(): void
    {
        $image = $this->exampleImage->getContents();
        $snap = new Snap(
            id: null,
            albumId: 5,
            image: $image,
            mimeType: MimeTypeEnum::ImageWebp,
            orientation: Orientation::PORTRAIT
        );

        $repository = new SnapRepository($this->container?->get(Connection::class));
        $result = $repository->store($snap);

        $this->assertInstanceOf(Snap::class, $result);
        $this->assertSame(100, $result->getId());
        $this->assertSame(5, $result->getAlbumId());
        $this->assertSame(MimeTypeEnum::ImageWebp, $result->getMimeType());
        $this->assertSame(Orientation::PORTRAIT, $result->getOrientation());
        $this->assertSame($image, $result->getImage());
    }

    public function testAll(): void
    {
        $repository = new SnapRepository($this->container?->get(Connection::class));
        $result = $repository->all();

        $this->assertIsIterable($result);
        $this->assertInstanceOf(Snap::class, $result[0]);
        $this->assertSame(1, $result[0]->getId());
    }

    public function testOfAlbumId(): void
    {
        $repository = new SnapRepository($this->container?->get(Connection::class));

        $snap = new Snap(
            id: null,
            albumId: 42,
            image: $this->exampleImage->getContents(),
            mimeType: MimeTypeEnum::ImageWebp,
            orientation: Orientation::PORTRAIT
        );
        $repository->store($snap);

        $results = $repository->ofAlbumId(42);

        $this->assertIsIterable($results);
        $this->assertNotEmpty($results);
        foreach ($results as $record) {
            $this->assertInstanceOf(Snap::class, $record);
            $this->assertSame(42, $record->getAlbumId());
        }
    }

    public function testUpdate(): void
    {
        $snap = new Snap(
            id: null,
            albumId: 1,
            image: $this->exampleImage->getContents(),
            mimeType: MimeTypeEnum::ImageWebp,
            orientation: Orientation::PORTRAIT
        );

        $repository = new SnapRepository($this->container?->get(Connection::class));
        $newSnap = $repository->store($snap);

        $updatedSnap = $newSnap->cloneWith(albumId: 2);
        $result = $repository->update($updatedSnap);

        $this->assertInstanceOf(Snap::class, $result);
        $this->assertSame($updatedSnap->getId(), $result->getId());
        $this->assertSame(2, $result->getAlbumId());
    }

    public function testDestroy(): void
    {
        $snap = new Snap(
            id: null,
            albumId: 1,
            image: $this->exampleImage->getContents(),
            mimeType: MimeTypeEnum::ImageWebp,
            orientation: Orientation::PORTRAIT
        );

        $repository = new SnapRepository($this->container?->get(Connection::class));
        $newSnap = $repository->store($snap);

        $this->assertInstanceOf(Snap::class, $newSnap);

        $repository->destroy($newSnap);

        $this->expectException(DomainRecordNotFoundException::class);
        $repository->ofId($newSnap->getId());
    }

    public function testDestroyNonexistentSnap(): void
    {
        $snap = new Snap(
            id: 99999999,
            albumId: 1,
            image: $this->exampleImage->getContents(),
            mimeType: MimeTypeEnum::ImageWebp,
            orientation: Orientation::PORTRAIT
        );
        $repository = new SnapRepository($this->container?->get(Connection::class));

        $this->expectException(DomainRecordNotFoundException::class);
        $repository->destroy($snap);
    }

    public function testStoreThrowsExceptionWhenWrongEntityProvided(): void
    {
        $invalidClass = new stdClass();
        $repository = new SnapRepository($this->container?->get(Connection::class));

        $this->expectException(InvalidArgumentException::class);
        $repository->store($invalidClass);
    }

    public function testUpdateThrowsExceptionWhenWrongEntityProvided(): void
    {
        $invalidClass = new stdClass();
        $repository = new SnapRepository($this->container?->get(Connection::class));

        $this->expectException(InvalidArgumentException::class);
        $repository->update($invalidClass);
    }

    public function testDestroyThrowsExceptionWhenWrongEntityProvided(): void
    {
        $invalidClass = new stdClass();
        $repository = new SnapRepository($this->container?->get(Connection::class));

        $this->expectException(InvalidArgumentException::class);
        $repository->destroy($invalidClass);
    }

    public function testUpdateThrowsExceptionWhenEntityWithNullIdProvided(): void
    {
        $snap = new Snap(
            id: null,
            albumId: 1,
            image: $this->exampleImage->getContents(),
            mimeType: MimeTypeEnum::ImageWebp,
            orientation: Orientation::PORTRAIT
        );
        $repository = new SnapRepository($this->container?->get(Connection::class));

        $this->expectException(DomainRecordNotFoundException::class);
        $repository->update($snap);
    }
}
