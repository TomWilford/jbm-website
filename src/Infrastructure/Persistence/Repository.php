<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;

abstract class Repository implements RepositoryInterface
{
    public function __construct(protected Connection $connection)
    {
        //
    }

    /**
     * Instantiate a new instance of QueryBuilder. To be used for each repository method to avoid state pollution.
     */
    public function getQueryBuilder(): QueryBuilder
    {
        return $this->connection->createQueryBuilder();
    }

    /**
     * Fetch records from the database with optional criteria, limits, and sorting.
     *
     * @param string $table The name of the table.
     * @param array<string, mixed> $criteria Key-value pairs for WHERE conditions.
     * @param int|null $limit Optional limit for the number of records.
     * @param string|null $orderBy Column to sort by.
     * @param string $orderDirection Sorting direction: ASC or DESC.
     * @return array<array<string, mixed>> The result set as an array of associative arrays.
     * @throws Exception
     */
    protected function fetch(
        string $table,
        array $criteria = [],
        ?int $limit = null,
        ?string $orderBy = null,
        string $orderDirection = 'ASC'
    ): array {
        $qb = $this->getQueryBuilder();
        $qb->select('*')->from($table);

        foreach ($criteria as $key => $value) {
            $qb->andWhere("{$key} = :{$key}")->setParameter($key, $value);
        }

        if ($orderBy) {
            $qb->orderBy($orderBy, $orderDirection);
        }

        if ($limit !== null) {
            $qb->setMaxResults($limit);
        }

        return $qb->executeQuery()->fetchAllAssociative();
    }

    /**
     * @param array<mixed> $array
     * @return array<mixed>
     */
    public function arrayToObjects(array $array): array
    {
        $objects = [];

        foreach ($array as $key => $item) {
            $objects[$key] = $this->arrayToObject($item);
        }

        return $objects;
    }
}
