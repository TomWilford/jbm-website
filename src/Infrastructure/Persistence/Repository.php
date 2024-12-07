<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Infrastructure\Database\DatabaseInterface;

abstract class Repository implements RepositoryInterface
{
    public function __construct(protected DatabaseInterface $database)
    {
        //
    }

    public function arrayToObjects(array $array): array
    {
        $objects = [];

        foreach ($array as $key => $item) {
            $objects[$key] = $this->arrayToObject($item);
        }

        return $objects;
    }
}