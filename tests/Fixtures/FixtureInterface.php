<?php

namespace App\Test\Fixtures;

interface FixtureInterface
{
    public function getTable(): string;

    /**
     * @return array{array{mixed}}
     */
    public function getRecords(): array;
}
