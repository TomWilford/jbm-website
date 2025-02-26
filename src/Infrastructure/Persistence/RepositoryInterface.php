<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

interface RepositoryInterface
{
    public function __construct(Connection $connection);

    public function getQueryBuilder(): QueryBuilder;

    public function store(object $entity): object;

    /**
     * @return array<object>
     */
    public function all(): iterable;

    public function ofId(int $id): object;

    public function update(object $entity): object;

    public function destroy(object $entity): void;

    /**
     * @param array<string, mixed> $array
     */
    public function arrayToObject(array $array): object;

    /**
     * @param array<string, mixed> $array
     *
     * @return array{object}
     */
    public function arrayToObjects(array $array): array;
}
