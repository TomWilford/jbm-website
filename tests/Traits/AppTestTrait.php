<?php

namespace App\Test\Traits;

use App\Infrastructure\Database\DatabaseInterface;
use App\Test\Fixtures\ThingFixture;
use DI\ContainerBuilder;
use Doctrine\DBAL\Connection;
use Slim\App;
use UnexpectedValueException;

trait AppTestTrait
{
    use ArrayTestTrait;
    use ContainerTestTrait;
    use HttpTestTrait;
    use HttpJsonTestTrait;

    protected App $app;

    /**
     * Before each test.
     */
    protected function setUp(): void
    {
        $this->setUpApp();
    }

    protected function setUpApp(): void
    {
        $container = (new ContainerBuilder())
            ->addDefinitions(__DIR__ . '/../../config/container.php')
            ->build();

        $this->app = $container->get(App::class);

        $this->setUpContainer($container);

        // If we're in a class that uses DatabaseTestTrait
        if (method_exists($this, 'initialiseTestDatabase')) {
            // Check that the test config is set up to use pdo:///sqlite:memory:
            if (!str_contains($this->container->get('settings')['db']['dsn'], 'memory')) {
                throw new UnexpectedValueException('Test database name MUST contain the word "memory"');
            }
            // Pass through DatabaseInterface & existing (Doctrine) Connection
            $this->database = $this->container->get(DatabaseInterface::class);
            $this->connection = $this->container->get(Connection::class);
            // Find the migrations and execute them to build the test database
            $this->initialiseTestDatabase();
            // Save any fixtures to the test database for unit tests
            if (method_exists($this, 'insertDefaultFixtureRecords')) {
                $this->insertDefaultFixtureRecords([ThingFixture::class]);
            }
        }
    }
}
