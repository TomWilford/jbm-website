<?php

declare(strict_types=1);

namespace App\Test\TestCase\Module\Bit\Create\Domain;

use App\Module\Bit\Create\Domain\BitCreator;
use App\Module\Bit\Data\Bit;
use App\Module\Bit\Enum\Language;
use App\Module\Bit\Infrastructure\BitRepository;
use App\Test\Traits\AppTestTrait;
use App\Test\Traits\DatabaseTestTrait;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[UsesClass(BitCreator::class)]
class BitCreatorTest extends TestCase
{
    use AppTestTrait;
    use DatabaseTestTrait;

    public function testDataTransformsSuccessfully(): void
    {
        $data = [
            'name' => 'Test Bit',
            'code' => "var_dump(sprintf('%s %s!', 'Hello', 'World'));",
            'language' => 'PHP',
            'description' => 'Test bit description',
            'returns' => 'string(12) "Hello World!"',
        ];

        $repository = new BitRepository($this->container?->get(Connection::class));
        $creator = new BitCreator($repository);

        $result = $creator->createFromArray($data);

        $this->assertInstanceOf(Bit::class, $result);
        $this->assertSame('Test Bit', $result->getName());
        $this->assertSame(Language::PHP, $result->getLanguage());
    }

    public function testAlternativeDataTransformsSuccessfully(): void
    {
        $data = [
            'name' => 'Test Bit',
            'code' => "var_dump(sprintf('%s %s!', 'Hello', 'World'));",
            'language' => 'php',
            'description' => '',
            'returns' => '',
        ];

        $repository = new BitRepository($this->container?->get(Connection::class));
        $creator = new BitCreator($repository);

        $result = $creator->createFromArray($data);

        $this->assertInstanceOf(Bit::class, $result);
        $this->assertSame('Test Bit', $result->getName());
        $this->assertSame(Language::PHP, $result->getLanguage());
        $this->assertNull($result->getDescription());
    }
}
