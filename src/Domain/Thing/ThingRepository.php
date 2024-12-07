<?php

declare(strict_types=1);

namespace App\Domain\Thing;

use App\Infrastructure\Persistence\Repository;

class ThingRepository extends Repository
{
    /**
     * @return Thing[]
     */
    public function all(): iterable
    {
        return $this->arrayToObjects($this->database->query('SELECT * FROM things'));
    }

    public function ofId(int $id): Thing
    {
        return $this->arrayToObject($this->database->query('SELECT * FROM things WHERE id = :id', ['id' => $id]));
    }

    public function arrayToObject(array $array): Thing
    {
        return new Thing(
            $array['id'],
            $array['name'],
            $array['description'],
            $array['short_description'],
            $array['image'],
            $array['url'],
            $array['created_at'],
            $array['updated_at']
        );
    }
}