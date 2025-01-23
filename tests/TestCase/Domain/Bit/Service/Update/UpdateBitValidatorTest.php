<?php

declare(strict_types=1);

namespace App\Test\TestCase\Domain\Bit\Service\Update;

use App\Domain\Bit\Service\Update\UpdateBitValidator;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Respect\Validation\Exceptions\ValidationException;

#[UsesClass(UpdateBitValidator::class)]
class UpdateBitValidatorTest extends TestCase
{
    private UpdateBitValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new UpdateBitValidator();
    }

    public function testValidateValidData(): void
    {
        $data = [
            'name' => 'Test Name',
            'code' => 'ABC123',
            'language' => 'PHP',
            'description' => 'A valid description.',
        ];

        $this->expectNotToPerformAssertions();
        $this->validator->validate($data);
    }

    public function testValidateValidDataWithOptionalFieldsMissing(): void
    {
        $data = [
            'name' => 'Test Name',
            'code' => '',
            'language' => '',
            'description' => '',
        ];

        $this->expectNotToPerformAssertions(); // Validation should pass without exceptions
        $this->validator->validate($data);
    }

    public function testValidateInvalidName(): void
    {
        $data = [
            'name' => str_repeat('A', 67), // Invalid: too long
            'code' => "var_dump(sprintf('%s %s!', 'Hello', 'World'));",
            'language' => 'PHP',
            'description' => 'A valid description.',
        ];

        $this->expectException(ValidationException::class);
        $this->validator->validate($data);
    }

    public function testValidateInvalidLanguage(): void
    {
        $data = [
            'name' => 'Test Name',
            'code' => "var_dump(sprintf('%s %s!', 'Hello', 'World'));",
            'language' => 'capybara', // Invalid: Not in Language::values()
            'description' => 'A valid description.',
        ];

        $this->expectException(ValidationException::class);
        $this->validator->validate($data);
    }

    public function testValidateInvalidDescription(): void
    {
        $data = [
            'name' => 'Test Name',
            'code' => "var_dump(sprintf('%s %s!', 'Hello', 'World'));",
            'language' => 'PHP',
            'description' => str_repeat('A', 256), // Exceeds max length of 255
        ];

        $this->expectException(ValidationException::class);
        $this->validator->validate($data);
    }
}
