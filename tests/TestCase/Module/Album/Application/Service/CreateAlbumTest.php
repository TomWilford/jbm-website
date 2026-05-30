<?php

declare(strict_types=1);

namespace App\Test\TestCase\Module\Album\Application\Service;

use App\Module\Album\Application\Service\CreateAlbum;
use App\Module\Album\Application\Validator\CreateAlbumValidator;
use App\Module\Album\Domain\Album;
use App\Module\Album\Domain\Camera;
use App\Module\Album\Infrastructure\AlbumRepository;
use App\Test\Traits\AppTestTrait;
use App\Test\Traits\DatabaseTestTrait;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Respect\Validation\Exceptions\ValidationException;

#[CoversClass(CreateAlbum::class)]
class CreateAlbumTest extends TestCase
{
    use AppTestTrait;
    use DatabaseTestTrait;

    public function testDataTransformsAndPersistsSuccessfully(): void
    {
        $data = [
            'name' => 'Tokyo Street Photography',
            'camera' => 'olympusPen',
            'location' => 'Japan',
            'date' => '2025-04-12',
        ];

        $validator = new CreateAlbumValidator();
        $repository = new AlbumRepository($this->container?->get(Connection::class));
        $creator = new CreateAlbum($validator, $repository);

        $result = $creator->createFromArray($data);

        $this->assertInstanceOf(Album::class, $result);
        $this->assertGreaterThan(0, $result->getId());
        $this->assertSame('Tokyo Street Photography', $result->getName());
        $this->assertSame(Camera::OLYMPUS_PEN, $result->getCamera());
        $this->assertSame('Japan', $result->getLocation());
        $this->assertSame('2025-04-12', $result->getDate());
        $this->assertNotNull($result->getCreatedAt());
        $this->assertNotNull($result->getUpdatedAt());
    }

    public function testThrowsExceptionWhenValidatorFails(): void
    {
        $stubValidator = $this->createMock(CreateAlbumValidator::class);

        $exception = $this->createStub(ValidationException::class);

        $stubValidator->method('validate')
            ->willThrowException($exception);

        $repository = new AlbumRepository($this->container?->get(Connection::class));

        $creator = new CreateAlbum($stubValidator, $repository);

        $invalidData = [
            'name' => '',
            'camera' => 'invalid-camera',
            'location' => 'Nowhere',
            'date' => 'not-a-date',
        ];

        $this->expectException(ValidationException::class);

        $creator->createFromArray($invalidData);
    }
}
