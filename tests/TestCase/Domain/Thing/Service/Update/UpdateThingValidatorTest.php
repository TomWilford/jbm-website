<?php

declare(strict_types=1);

namespace App\Test\TestCase\Domain\Thing\Service\Update;

use App\Domain\Thing\Enum\FaultLevel;
use App\Domain\Thing\Service\Update\UpdateThingValidator;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Respect\Validation\Exceptions\ValidationException;

#[UsesClass(UpdateThingValidator::class)]
class UpdateThingValidatorTest extends TestCase
{
    private UpdateThingValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new UpdateThingValidator();
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

    public function testValidateWithSingleOptionalValue(): void
    {
        $data = [
            'name' => '', // Empty string to represent optional field
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

    public function testValidateWithNullableValues(): void
    {
        $data = [
            'name' => '',
            'short_description' => '',
            'description' => '',
            'featured' => '',
            'url' => 'null', // Valid: Null
            'fault_level' => '',
            'active_from' => '',
            'active_to' => 'null', // Valid: null
        ];

        $this->expectNotToPerformAssertions();
        $this->validator->validate($data);
    }

    public function testInvalidValueThrowsException(): void
    {
        $data = [
            'name' => '',
            'short_description' => '',
            'description' => '',
            'featured' => '',
            'url' => '123', // Invalid
            'fault_level' => '',
            'active_from' => '',
            'active_to' => '123', // Invalid
        ];

        $this->expectException(ValidationException::class);
        $this->validator->validate($data);
    }
}
