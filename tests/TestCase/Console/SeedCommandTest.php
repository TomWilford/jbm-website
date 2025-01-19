<?php

declare(strict_types=1);

namespace App\Test\TestCase\Console;

use App\Console\SeedCommand;
use App\Database\Seeds\SeedInterface;
use App\Domain\Thing\Repository\ThingRepository;
use App\Domain\Thing\Thing;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class SeedCommandTest extends TestCase
{
    public function testExecuteSeedsDataSuccessfully(): void
    {
        // Mock SeedInterface
        $seedMock = $this->createMock(SeedInterface::class);
        $repositoryMock = $this->createMock(ThingRepository::class);

        $thingMock1 = $this->createMock(Thing::class);
        $thingMock2 = $this->createMock(Thing::class);

        $seedMock->method('getName')->willReturn('TestSeed');
        $seedMock->method('getData')->willReturn([$thingMock1, $thingMock2]);
        $seedMock->method('getRepository')->willReturn($repositoryMock);

        // Expect repository's store method to be called for each entity
        $repositoryMock->expects($this->exactly(2))
            ->method('store')
            ->willReturnOnConsecutiveCalls($thingMock1, $thingMock2);

        // Create the command instance with mocked seeds
        $command = new SeedCommand([$seedMock]);

        // Use CommandTester to test the command
        $commandTester = new CommandTester($command);

        // Execute the command
        $commandTester->execute([]);

        // Assert the command output
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Importing Seed Data', $output);
        $this->assertStringContainsString('Seeding TestSeed', $output);
        $this->assertStringContainsString('Seed Data Imported', $output);

        // Assert the command returned a success status code
        $this->assertEquals(0, $commandTester->getStatusCode());
    }
}
