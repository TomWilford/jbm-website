<?php

namespace App\Test\Traits;

use Doctrine\DBAL\Connection;
use Doctrine\Migrations\Configuration\Connection\ExistingConnection;
use Doctrine\Migrations\Configuration\Migration\PhpFile;
use Doctrine\Migrations\DependencyFactory;

trait DatabaseConnectionTestTrait
{
    protected Connection $connection;
    protected DependencyFactory $dependencyFactory;

    /**
     * Open up the migrations.php configuration file and pass through existing connection to set up DependencyFactory
     * so that we can find/execute any stored migrations in our test database.
     */
    protected function setupDependencyFactory(): void
    {
        $migrationConfigFile = dirname(__DIR__, 2) . '/migrations.php';
        $config = new PhpFile($migrationConfigFile);
        $this->dependencyFactory = DependencyFactory::fromConnection(
            $config,
            new ExistingConnection($this->connection)
        );
    }
}
