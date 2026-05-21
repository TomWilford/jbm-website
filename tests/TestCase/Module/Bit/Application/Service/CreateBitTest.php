<?php

declare(strict_types=1);

namespace App\Test\TestCase\Module\Bit\Application\Service;

use App\Module\Bit\Application\Service\CreateBit;
use App\Module\Bit\Domain\Bit;
use App\Module\Bit\Domain\Language;
use App\Module\Bit\Infrastructure\BitRepository;
use App\Test\Traits\AppTestTrait;
use App\Test\Traits\DatabaseTestTrait;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(CreateBit::class)]
class CreateBitTest extends TestCase
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
        $creator = new CreateBit($repository);

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
        $creator = new CreateBit($repository);

        $result = $creator->createFromArray($data);

        $this->assertInstanceOf(Bit::class, $result);
        $this->assertSame('Test Bit', $result->getName());
        $this->assertSame(Language::PHP, $result->getLanguage());
        $this->assertNull($result->getDescription());
    }
}
