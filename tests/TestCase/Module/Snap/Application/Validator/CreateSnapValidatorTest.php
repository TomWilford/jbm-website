<?php

declare(strict_types=1);

namespace App\Test\TestCase\Module\Snap\Application\Validator;

use App\Module\Snap\Application\Validator\CreateSnapValidator;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Stream;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;
use Respect\Validation\Exceptions\ValidationException;

#[CoversClass(CreateSnapValidator::class)]
class CreateSnapValidatorTest extends TestCase
{
    private CreateSnapValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new CreateSnapValidator();
    }

    private function createMockUploadedFile(
        int $size = 51200,
        int $error = UPLOAD_ERR_OK,
        string $clientFilename = 'test.webp',
        string $mediaType = 'image/webp',
    ): UploadedFileInterface {
        return (new Psr17Factory())->createUploadedFile(
            new Stream(fopen('php://temp', 'r+')),
            $size,
            $error,
            $clientFilename,
            $mediaType
        );
    }

    public function testValidateWithValidData(): void
    {
        $mockFile = $this->createMockUploadedFile();

        $data = [
            'album_sqid' => 'Uk',
            'image' => $mockFile,
        ];

        $this->expectNotToPerformAssertions();
        $this->validator->validate($data);
    }

    public function testValidateWithInvalidAlbumId(): void
    {
        $mockFile = $this->createMockUploadedFile();

        $data = [
            'album_sqid' => '',
            'image' => $mockFile,
        ];

        $this->expectException(ValidationException::class);
        $this->validator->validate($data);
    }

    public function testValidateWithUnsupportedMimeType(): void
    {
        $mockFile = $this->createMockUploadedFile(mediaType: 'image/x-adobe-dng');

        $data = [
            'album_sqid' => 'Uk',
            'image' => $mockFile,
        ];

        $this->expectException(ValidationException::class);
        $this->validator->validate($data);
    }

    public function testValidateWithMissingMediaType(): void
    {
        $mockFile = $this->createMockUploadedFile(mediaType: '');

        $data = [
            'album_sqid' => 'Uk',
            'image' => $mockFile,
        ];

        $this->expectException(ValidationException::class);
        $this->validator->validate($data);
    }

    public function testValidateWithFileExceedingMaxSize(): void
    {
        $hugeSize = 12 * 1024 * 1024;
        $mockFile = $this->createMockUploadedFile(size: $hugeSize, mediaType: '');

        $data = [
            'album_sqid' => 'Uk',
            'image' => $mockFile,
        ];

        $this->expectException(ValidationException::class);
        $this->validator->validate($data);
    }

    public function testValidateWithMissingRequiredField(): void
    {
        $data = [
            'image' => $this->createMockUploadedFile(),
        ];

        $this->expectException(ValidationException::class);
        /* @phpstan-ignore-next-line */
        $this->validator->validate($data);
    }
}
