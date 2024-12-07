<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Tools\DsnParser;

class SQLiteDatabase implements DatabaseInterface
{
    private Connection $conn;

    public function __construct(string $dsn)
    {
        $dsnParser = new DsnParser();
        $connectionParams = $dsnParser->parse($dsn);

        $this->conn = DriverManager::getConnection($connectionParams);
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