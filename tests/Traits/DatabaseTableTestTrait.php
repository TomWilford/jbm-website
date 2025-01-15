<?php

namespace App\Test\Traits;

/**
 * A utility trait for managing database fixtures in tests.
 *
 * This trait provides methods to insert predefined fixture records into database tables,
 * streamlining the process of setting up test data. It supports dynamic fixture classes
 * and generates SQL queries based on the structure of the provided records.
 *
 * Methods:
 * - `insertDefaultFixtureRecords(array $fixtures)`: Iterates over an array of fixture class names,
 *   processes each fixture, and inserts its records into the corresponding table.
 * - `processFixture(string $className)`: Instantiates a fixture class, retrieves its target table
 *   and records, and inserts the data into the table using a dynamically generated SQL query.
 *   If the insertion fails, an exception is thrown with details about the failure.
 *
 * Usage:
 * - Create fixture classes that implement methods for defining the target table and the data records.
 * - Call `insertDefaultFixtureRecords` with the list of fixture class names to populate the database.
 */
trait DatabaseTableTestTrait
{
    /**
     * @param array{class-string|string} $fixtures
     */
    protected function insertDefaultFixtureRecords(array $fixtures): void
    {
        foreach ($fixtures as $className) {
            $this->processFixture($className);
        }
    }

    /**
     * @var class-string|string $className
     */
    protected function processFixture(string $className): void
    {
        $fixture = new $className();
        $table = $fixture->getTable();
        /** @var array $record */
        foreach ($fixture->getRecords() as $record) {
            $this->processRecord($record, $table);
        }
    }

    protected function processRecord(array $record, string $table): void
    {
        try {
            $qb = $this->connection->createQueryBuilder();
            $qb->insert($table);

            foreach ($record as $column => $value) {
                $qb->setValue($column, ':' . $column);
                $qb->setParameter($column, $value);
            }

            $qb->executeStatement();
        } catch (\Throwable $exception) {
            throw new \RuntimeException(
                "Failed to insert record into {$table}: " . $exception->getMessage(),
                0,
                $exception
            );
        }
    }
}
