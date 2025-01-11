<?php

namespace App\Test\Traits;

trait DatabaseTestTrait
{
    use DatabaseConnectionTestTrait;
    use DatabaseSchemaTestTrait;
    use DatabaseTableTestTrait;

    protected function initialiseTestDatabase(): void
    {
        $this->setupDependencyFactory();
        $this->runMigrations();
    }
}
