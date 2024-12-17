<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Infrastructure\Database\DatabaseInterface;

interface RepositoryInterface
{
    public function __construct(DatabaseInterface $database);

    public function arrayToObject(array $array): object;

    public function arrayToObjects(array $array): array;
}