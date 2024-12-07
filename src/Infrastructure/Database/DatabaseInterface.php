<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

interface DatabaseInterface
{
    public function query(string $query, array $params = []): array;
}