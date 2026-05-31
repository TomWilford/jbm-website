<?php

declare(strict_types=1);

namespace App\Test\TestCase\Module\Album\Application\Validator;

use App\Module\Album\Application\Validator\CreateAlbumValidator;
use App\Module\Album\Domain\Camera;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Respect\Validation\Exceptions\ValidationException;

#[CoversClass(CreateAlbumValidator::class)]
class CreateAlbumValidatorTest extends TestCase
{
    private CreateAlbumValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new CreateAlbumValidator();
    }

    public function testValidateWithValidData(): void
    {
        $data = [
            'name' => 'Summer Holiday 2025',
            'camera' => Camera::OLYMPUS_PEN->value,
            'location' => 'Greece',
            'date' => '2025-08-15',
            'sort_date' => '2025-08-15',
        ];

        $this->expectNotToPerformAssertions();
        $this->validator->validate($data);
    }

    public function testValidateWithInvalidName(): void
    {
        $data = [
            'name' => '', // Invalid: Empty string
            'camera' => Camera::OLYMPUS_PEN->value,
            'location' => 'Greece',
            'date' => '2025-08-15',
            'sort_date' => '2025-08-15',
        ];

        $this->expectException(ValidationException::class);
        $this->validator->validate($data);
    }

    public function testValidateWithInvalidCameraEnum(): void
    {
        $data = [
            'name' => 'Summer Holiday 2025',
            'camera' => 'Nikon_D850', // Invalid: Not an allowed value in Camera backed enum
            'location' => 'Greece',
            'date' => '2025-08-15',
            'sort_date' => '2025-08-15',
        ];

        $this->expectException(ValidationException::class);
        $this->validator->validate($data);
    }

    public function testValidateWithInvalidLocation(): void
    {
        $data = [
            'name' => 'Summer Holiday 2025',
            'camera' => Camera::OLYMPUS_PEN->value,
            'location' => str_repeat('A', 256), // Invalid: Exceeds maximum length of 255
            'date' => '2025-08-15',
            'sort_date' => '2025-08-15',
        ];

        $this->expectException(ValidationException::class);
        $this->validator->validate($data);
    }

    public function testValidateWithMissingDate(): void
    {
        $data = [
            'name' => 'Summer Holiday 2025',
            'camera' => Camera::OLYMPUS_PEN->value,
            'location' => 'Somewhere',
            'date' => '', // Invalid: string required
            'sort_date' => '2025-08-15',
        ];

        $this->expectException(ValidationException::class);
        $this->validator->validate($data);
    }

    public function testValidateWithMissingSortDate(): void
    {
        $data = [
            'name' => 'Summer Holiday 2025',
            'camera' => Camera::OLYMPUS_PEN->value,
            'location' => 'Somewhere',
            'date' => '2025-08-15',
            'sort_date' => '', // Invalid: date required
        ];

        $this->expectException(ValidationException::class);
        $this->validator->validate($data);
    }

    public function testValidateWithInvalidSortDate(): void
    {
        $data = [
            'name' => 'Summer Holiday 2025',
            'camera' => Camera::OLYMPUS_PEN->value,
            'location' => 'Somewhere',
            'date' => '2025-08-15',
            'sort_date' => 'foobarbaz', // Invalid: incorrect format
        ];

        $this->expectException(ValidationException::class);
        $this->validator->validate($data);
    }

    public function testValidateWithMissingRequiredField(): void
    {
        $data = [
            // Missing 'name'
            'camera' => Camera::OLYMPUS_PEN->value,
            'location' => 'Greece',
            'date' => '2025-08-15',
        ];

        $this->expectException(ValidationException::class);
        /* @phpstan-ignore-next-line */
        $this->validator->validate($data);
    }
}
