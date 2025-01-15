<?php

declare(strict_types=1);

namespace App\Test\Fixtures;

interface FixtureInterface
{
    public function getTable(): string;

    /**
     * @return array{array{mixed}}
     */
    public function getRecords(): array;
}
