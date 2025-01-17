<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

interface RepositoryInterface
{
    public function __construct(Connection $connection);

    public function getQueryBuilder(): QueryBuilder;

    /**
     * @param array<string, mixed> $array
     */
    public function arrayToObject(array $array): object;

    /**
     * @param array<string, mixed> $array
     * @return array{object}
     */
    public function arrayToObjects(array $array): array;
}
