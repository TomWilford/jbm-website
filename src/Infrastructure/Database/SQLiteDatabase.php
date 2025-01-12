<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

readonly class SQLiteDatabase implements DatabaseInterface
{
    public function __construct(private Connection $conn)
    {
        //
    }

    /**
     * @throws Exception
     */
    public function query(string $query, array $params = []): array
    {
        $statement = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $statement->bindValue($key, $value);
        }

        $results = $statement->executeQuery();

        return $results->fetchAllAssociative();
    }
}
