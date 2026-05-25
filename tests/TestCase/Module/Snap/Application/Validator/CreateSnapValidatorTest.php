<?php

declare(strict_types=1);

namespace App\Test\TestCase\Module\Snap\Application\Validator;

use App\Module\Snap\Application\Validator\CreateSnapValidator;
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
        string $mediaType = 'image/webp',
        int $error = UPLOAD_ERR_OK,
        int $size = 51200,
    ): UploadedFileInterface {
        $mock = $this->createMock(UploadedFileInterface::class);
        $mock->method('getClientMediaType')->willReturn($mediaType);
        $mock->method('getError')->willReturn($error);
        $mock->method('getSize')->willReturn($size);

        return $mock;
    }

    public function testValidateWithValidData(): void
    {
        $mockFile = $this->createMockUploadedFile();

        $data = [
            'album_id' => 12,
            'file' => $mockFile,
        ];

        $this->expectNotToPerformAssertions();
        $this->validator->validate($data);
    }

    public function testValidateWithInvalidAlbumId(): void
    {
        $mockFile = $this->createMockUploadedFile();

        $data = [
            'album_id' => '',
            'file' => $mockFile,
        ];

        $this->expectException(ValidationException::class);
        $this->validator->validate($data);
    }

    public function testValidateWithUnsupportedMimeType(): void
    {
        $mockFile = $this->createMockUploadedFile('image/x-adobe-dng');

        $data = [
            'album_id' => 12,
            'file' => $mockFile,
        ];

        $this->expectException(ValidationException::class);
        $this->validator->validate($data);
    }

    public function testValidateWithMissingMediaType(): void
    {
        $mockFile = $this->createMock(UploadedFileInterface::class);
        $mockFile->method('getClientMediaType')->willReturn(null);
        $mockFile->method('getError')->willReturn(UPLOAD_ERR_OK);

        $data = [
            'album_id' => 12,
            'file' => $mockFile,
        ];

        $this->expectException(ValidationException::class);
        $this->validator->validate($data);
    }

    public function testValidateWithFileExceedingMaxSize(): void
    {
        $hugeSize = 12 * 1024 * 1024;
        $mockFile = $this->createMockUploadedFile('image/webp', UPLOAD_ERR_OK, $hugeSize);

        $data = [
            'album_id' => 12,
            'file' => $mockFile,
        ];

        $this->expectException(ValidationException::class);
        $this->validator->validate($data);
    }

    public function testValidateWithMissingRequiredField(): void
    {
        $data = [
            'file' => $this->createMockUploadedFile(),
        ];

        $this->expectException(ValidationException::class);
        /* @phpstan-ignore-next-line */
        $this->validator->validate($data);
    }
}
