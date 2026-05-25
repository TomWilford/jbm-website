<?php

declare(strict_types=1);

namespace App\Test\TestCase\Module\Snap\Application\Service;

use App\Module\Snap\Application\Service\CreateSnap;
use App\Module\Snap\Application\Validator\CreateSnapValidator;
use App\Module\Snap\Domain\Snap;
use App\Module\Snap\Infrastructure\SnapRepository;
use App\Test\Traits\AppTestTrait;
use App\Test\Traits\DatabaseTestTrait;
use Doctrine\DBAL\Connection;
use Nyholm\Psr7\Stream;
use Nyholm\Psr7\UploadedFile;
use PhpCommonEnums\MimeType\Enumeration\MimeTypeEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Respect\Validation\Exceptions\ValidationException;

#[CoversClass(CreateSnap::class)]
class CreateSnapTest extends TestCase
{
    use AppTestTrait;
    use DatabaseTestTrait;

    private function createUploadedFileFixture(): UploadedFile
    {
        $filePath = dirname(__DIR__, 5) . '/Fixtures/assets/snap-01.webp';

        $stream = new Stream(fopen($filePath, 'r'));
        $size = filesize($filePath) ?: 0;

        return new UploadedFile(
            $stream,
            $size,
            UPLOAD_ERR_OK,
            'snap-01.webp',
            'image/webp'
        );
    }

    public function testDataTransformsAndPersistsSuccessfully(): void
    {
        $uploadedFile = $this->createUploadedFileFixture();

        $data = [
            'album_id' => 1,
            'image' => $uploadedFile,
        ];

        $validator = new CreateSnapValidator();
        $repository = new SnapRepository($this->container?->get(Connection::class));
        $creator = new CreateSnap($validator, $repository);

        $result = $creator->createFromArray($data);

        $uploadedFile->getStream()->rewind();
        $expectedBinary = $uploadedFile->getStream()->getContents();

        $this->assertInstanceOf(Snap::class, $result);
        $this->assertGreaterThan(0, $result->getId());
        $this->assertSame(1, $result->getAlbumId());
        $this->assertSame(MimeTypeEnum::ImageWebp, $result->getMimeType());
        $this->assertSame($expectedBinary, $result->getImage());
        $this->assertNotNull($result->getCreatedAt());
        $this->assertNotNull($result->getUpdatedAt());
    }

    public function testThrowsExceptionWhenValidatorFails(): void
    {
        $stubValidator = $this->createMock(CreateSnapValidator::class);
        $exception = $this->createStub(ValidationException::class);

        $stubValidator->method('validate')
            ->willThrowException($exception);

        $repository = new SnapRepository($this->container?->get(Connection::class));
        $creator = new CreateSnap($stubValidator, $repository);

        $invalidData = [
            'album_id' => '', // Fails validator rules
            'image' => $this->createUploadedFileFixture(),
        ];

        $this->expectException(ValidationException::class);

        $creator->createFromArray($invalidData);
    }
}
