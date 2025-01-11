<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Infrastructure\Database\DatabaseInterface;

interface RepositoryInterface
{
    public function __construct(DatabaseInterface $database);

    /**
     * @param array{mixed} $array
     */
    public function arrayToObject(array $array): object;

    /**
     * @param array{mixed} $array
     * @return array{mixed}
     */
    public function arrayToObjects(array $array): array;
}
