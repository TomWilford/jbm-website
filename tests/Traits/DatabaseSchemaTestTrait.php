<?php

namespace App\Test\Traits;

use Symfony\Component\Console\Input\ArrayInput;

trait DatabaseSchemaTestTrait
{
    /**
     * Executes all pending migrations up to the latest version.
     *
     * This method resolves the "latest" migration version alias and calculates
     * a migration plan using the DependencyFactory. It ensures the metadata storage
     * is initialized and uses a console input migrator configuration to properly
     * configure the migration process.
     *
     * The steps are:
     * - Resolve the latest migration version.
     * - Calculate the migration plan up to the resolved version.
     * - Initialize the metadata storage to ensure consistency.
     * - Configure the migrator using an empty console input.
     * - Execute the migrations defined in the calculated plan.
     */
    protected function runMigrations(): void
    {
        $version = $this->dependencyFactory->getVersionAliasResolver()->resolveVersionAlias('latest');
        $planCalculator = $this->dependencyFactory->getMigrationPlanCalculator();
        $plan = $planCalculator->getPlanUntilVersion($version);

        $migrator = $this->dependencyFactory->getMigrator();
        $migratorConfigurationFactory = $this->dependencyFactory->getConsoleInputMigratorConfigurationFactory();
        $migratorConfiguration = $migratorConfigurationFactory->getMigratorConfiguration(new ArrayInput([]));

        $this->dependencyFactory->getMetadataStorage()->ensureInitialized();

        $migrator->migrate($plan, $migratorConfiguration);
    }
}
