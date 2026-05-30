<?php

declare(strict_types=1);

namespace App\Test\TestCase\Application\Console;

use App\Application\Console\SeedCommand;
use App\Database\Seeds\SeedInterface;
use App\Module\Thing\Domain\Thing;
use App\Module\Thing\Infrastructure\ThingRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

#[CoversClass(SeedCommand::class)]
class SeedCommandTest extends TestCase
{
    public function testExecuteSeedsDataSuccessfully(): void
    {
        $seedMock = $this->createMock(SeedInterface::class);
        $repositoryMock = $this->createMock(ThingRepository::class);

        $thingMock1 = $this->createMock(Thing::class);
        $thingMock2 = $this->createMock(Thing::class);

        $seedMock->method('getName')->willReturn('TestSeed');
        $seedMock->method('getData')->willReturn([$thingMock1, $thingMock2]);
        $seedMock->method('getRepository')->willReturn($repositoryMock);

        $repositoryMock->expects($this->exactly(2))
            ->method('store')
            ->willReturnOnConsecutiveCalls($thingMock1, $thingMock2);

        $command = new SeedCommand([$seedMock]);
        $commandTester = new CommandTester($command);

        $commandTester->execute([]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Importing Seed Data', $output);
        $this->assertStringContainsString('Seeding TestSeed', $output);
        $this->assertStringContainsString('Seed Data Imported', $output);

        $this->assertSame(0, $commandTester->getStatusCode());
    }
}
