<?php

declare(strict_types=1);

namespace App\Test\TestCase\Module\Thing\Application\Validator;

use App\Module\Thing\Application\Validator\CreateThingValidator;
use App\Module\Thing\Domain\FaultLevel;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Respect\Validation\Exceptions\ValidationException;

#[CoversClass(CreateThingValidator::class)]
class CreateThingValidatorTest extends TestCase
{
    private CreateThingValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new CreateThingValidator();
    }

    public function testValidateWithValidData(): void
    {
        $data = [
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
        $this->validator->validate($data);
    }

    public function testValidateWithInvalidName(): void
    {
        $data = [
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
        $this->validator->validate($data);
    }

    public function testValidateWithInvalidFaultLevel(): void
    {
        $data = [
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
        $this->validator->validate($data);
    }

    public function testValidateWithMissingOptionalUrl(): void
    {
        $data = [
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
        $this->validator->validate($data);
    }

    public function testValidateWithInvalidActiveFrom(): void
    {
        $data = [
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
        $this->validator->validate($data);
    }

    public function testValidateWithMissingRequiredField(): void
    {
        $data = [
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
        $this->validator->validate($data);
    }
}
