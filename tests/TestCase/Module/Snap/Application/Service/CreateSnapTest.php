<?php

declare(strict_types=1);

namespace App\Test\TestCase\Module\Snap\Application\Service;

use App\Infrastructure\Exception\DomainRecordNotFoundException;
use App\Module\Snap\Application\Service\CreateSnap;
use App\Module\Snap\Application\Validator\CreateSnapValidator;
use App\Module\Snap\Domain\Snap;
use App\Module\Snap\Infrastructure\SnapRepository;
use App\Test\Traits\AppTestTrait;
use App\Test\Traits\DatabaseTestTrait;
use Doctrine\DBAL\Connection;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Stream;
use PhpCommonEnums\MimeType\Enumeration\MimeTypeEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;
use Respect\Validation\Exceptions\ValidationException;
use Sqids\Sqids;

#[CoversClass(CreateSnap::class)]
class CreateSnapTest extends TestCase
{
    use AppTestTrait;
    use DatabaseTestTrait;

    private function createMockUploadedFile(
        int $size = 51200,
        int $error = UPLOAD_ERR_OK,
        string $clientFilename = 'test.webp',
        string $mediaType = 'image/webp',
    ): UploadedFileInterface {
        $filePath = dirname(__DIR__, 5) . '/Fixtures/assets/snap-01.webp';

        return (new Psr17Factory())->createUploadedFile(
            new Stream(fopen($filePath, 'r+')),
            $size,
            $error,
            $clientFilename,
            $mediaType
        );
    }

    public function testDataTransformsAndPersistsSuccessfully(): void
    {
        $uploadedFile = $this->createMockUploadedFile();

        $data = [
            'album_id' => 'Uk',
            'image' => $uploadedFile,
        ];

        $validator = new CreateSnapValidator();
        $repository = new SnapRepository($this->container?->get(Connection::class));
        $sqids = $this->createStub(Sqids::class);
        $sqids->method('decode')->willReturn([1]);
        $creator = new CreateSnap($validator, $repository, $sqids);

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
        $sqids = $this->createStub(Sqids::class);
        $sqids->method('decode')->willReturn([]);
        $creator = new CreateSnap($stubValidator, $repository, $sqids);

        $invalidData = [
            'album_id' => '', // Fails validator rules
            'image' => $this->createMockUploadedFile(),
        ];

        $this->expectException(ValidationException::class);

        $creator->createFromArray($invalidData);
    }

    public function testThrowsExceptionWhenSqidCannotBeDecoded(): void
    {
        $stubValidator = $this->createMock(CreateSnapValidator::class);
        $repository = new SnapRepository($this->container?->get(Connection::class));
        $sqids = $this->createStub(Sqids::class);
        $sqids->method('decode')->willReturn([]);
        $creator = new CreateSnap($stubValidator, $repository, $sqids);

        $invalidData = [
            'album_id' => 'beepboop',
            'image' => $this->createMockUploadedFile(),
        ];

        $this->expectException(DomainRecordNotFoundException::class);

        $creator->createFromArray($invalidData);
    }
}
