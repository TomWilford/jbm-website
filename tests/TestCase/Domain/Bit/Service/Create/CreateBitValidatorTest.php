<?php

declare(strict_types=1);

namespace App\Test\TestCase\Domain\Bit\Service\Create;

use App\Domain\Bit\Service\Create\CreateBitValidator;
use PHPUnit\Framework\TestCase;
use Respect\Validation\Exceptions\ValidationException;

class CreateBitValidatorTest extends TestCase
{
    private CreateBitValidator $validator;

    public function setUp(): void
    {
        $this->validator = new CreateBitValidator();
    }

    public function testValidateWithValidData(): void
    {
        $data = [
            'name' => 'Test Bit',
            'code' => "var_dump(sprintf('%s %s!', 'Hello', 'World'));",
            'language' => 'PHP',
            'description' => 'Test bit description',
            'returns' => 'string(12) "Hello World!"',
        ];

        $this->expectNotToPerformAssertions();
        $this->validator->validate($data);
    }

    public function testValidateWithInvalidData(): void
    {
        $data = [
            'name' => '', // Invalid: Empty string
            'code' => "var_dump(sprintf('%s %s!', 'Hello', 'World'));",
            'language' => 'PHP',
            'description' => 'Test bit description',
            'returns' => 'string(12) "Hello World!"',
        ];

        $this->expectException(ValidationException::class);
        $this->validator->validate($data);
    }

    public function testValidateWithOptionalValue(): void
    {
        $data = [
            'name' => 'Test Bit',
            'code' => "var_dump(sprintf('%s %s!', 'Hello', 'World'));",
            'language' => 'PHP',
            'description' => '',
            'returns' => '',
        ];

        $this->expectNotToPerformAssertions();
        $this->validator->validate($data);
    }

    public function testValidateWithInvalidLanguage(): void
    {
        $data = [
            'name' => 'Test Bit',
            'code' => "var_dump(sprintf('%s %s!', 'Hello', 'World'));",
            'language' => 'capybara',  // Invalid: Not in Language::values()
            'description' => 'Test bit description',
            'returns' => 'string(12) "Hello World!"',
        ];

        $this->expectException(ValidationException::class);
        $this->validator->validate($data);
    }
}
