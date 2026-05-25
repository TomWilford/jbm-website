<?php

declare(strict_types=1);

namespace App\Test\Fixtures;

abstract class BaseFixture implements FixtureInterface
{
    protected string $table = '';
    protected array $records = [];

    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * {@inheritDoc}
     */
    public function getRecords(): array
    {
        return $this->records;
    }
}
