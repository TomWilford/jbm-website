<?php

declare(strict_types=1);

namespace App\Test\TestCase\Domain\Thing\Service;

use App\Domain\Thing\Enum\FaultLevel;
use App\Domain\Thing\Service\CreateThingValidator;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Respect\Validation\Exceptions\ValidationException;

#[UsesClass(CreateThingValidator::class)]
class CreateThingValidatorTest extends TestCase
{
    private CreateThingValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new CreateThingValidator();
    }

    public function testValidateWithValidData(): void
    {
        $validData = [
            'name' => 'Thing Name',
            'short_description' => 'Short Description',
            'description' => 'A detailed description of the thing.',
            'featured' => true,
            'url' => 'https://example.com',
            'fault_level' => FaultLevel::ALL->value,
            'active_from' => '2025-01-01',
            'active_to' => '2025-12-31',
        ];

        $this->expectNotToPerformAssertions();
        $this->validator->validate($validData);
    }

    public function testValidateWithInvalidName(): void
    {
        $invalidData = [
            'name' => '', // Invalid: Empty string
            'short_description' => 'Short Description',
            'description' => 'A detailed description of the thing.',
            'featured' => true,
            'url' => 'https://example.com',
            'fault_level' => FaultLevel::ALL->value,
            'active_from' => '2025-01-01',
            'active_to' => '2025-12-31',
        ];

        $this->expectException(ValidationException::class);
        $this->validator->validate($invalidData);
    }

    public function testValidateWithInvalidFaultLevel(): void
    {
        $invalidData = [
            'name' => 'Thing Name',
            'short_description' => 'Short Description',
            'description' => 'A detailed description of the thing.',
            'featured' => true,
            'url' => 'https://example.com',
            'fault_level' => 'INVALID_FAULT_LEVEL', // Invalid: Not in FaultLevel::values()
            'active_from' => '2025-01-01',
            'active_to' => '2025-12-31',
        ];

        $this->expectException(ValidationException::class);
        $this->validator->validate($invalidData);
    }

    public function testValidateWithMissingOptionalUrl(): void
    {
        $validData = [
            'name' => 'Thing Name',
            'short_description' => 'Short Description',
            'description' => 'A detailed description of the thing.',
            'featured' => true,
            'url' => null, // Valid: Optional field
            'fault_level' => FaultLevel::ALL->value,
            'active_from' => '2025-01-01',
            'active_to' => '2025-12-31',
        ];

        $this->expectNotToPerformAssertions();
        /* @phpstan-ignore-next-line */
        $this->validator->validate($validData);
    }

    public function testValidateWithInvalidActiveFrom(): void
    {
        $invalidData = [
            'name' => 'Thing Name',
            'short_description' => 'Short Description',
            'description' => 'A detailed description of the thing.',
            'featured' => true,
            'url' => 'https://example.com',
            'fault_level' => FaultLevel::ALL->value,
            'active_from' => 'invalid-date', // Invalid: Not a valid date
            'active_to' => '2025-12-31',
        ];

        $this->expectException(ValidationException::class);
        $this->validator->validate($invalidData);
    }

    public function testValidateWithMissingRequiredField(): void
    {
        $invalidData = [
            // Missing 'name'
            'short_description' => 'Short Description',
            'description' => 'A detailed description of the thing.',
            'featured' => true,
            'url' => 'https://example.com',
            'fault_level' => FaultLevel::ALL->value,
            'active_from' => '2025-01-01',
            'active_to' => '2025-12-31',
        ];

        $this->expectException(ValidationException::class);
        /* @phpstan-ignore-next-line */
        $this->validator->validate($invalidData);
    }
}
