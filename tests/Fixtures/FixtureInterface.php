<?php

namespace App\Test\Fixtures;

interface FixtureInterface
{
    public function getTable(): string;
    public function getRecords(): array;
}
