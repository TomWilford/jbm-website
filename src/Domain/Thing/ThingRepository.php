<?php

declare(strict_types=1);

namespace App\Domain\Thing;

use App\Domain\Enums\FaultLevel;
use App\Infrastructure\Persistence\Repository;

class ThingRepository extends Repository
{
    /** @return Thing[] */
    public function all(): iterable
    {
        return $this->arrayToObjects(
            $this->database->query('SELECT * FROM things')
        );
    }

    /** @return Thing[] */
    public function recent(): iterable
    {
        return $this->arrayToObjects(
            $this->database->query('SELECT * FROM things ORDER BY `from` DESC LIMIT 3')
        );
    }

    public function ofId(int $id): Thing
    {
        if (!$result = $this->database->query('SELECT * FROM things WHERE id = :id', ['id' => $id])) {
            throw new \Exception('Thing not found');
        }

        return $this->arrayToObject(
            $result[0]
        );
    }

    public function arrayToObject(array $array): Thing
    {
        return new Thing(
            $array['id'],
            $array['name'],
            $array['short_description'],
            $array['description'],
            $array['image'],
            $array['url'],
            FaultLevel::from($array['fault_level']),
            $array['from'],
            $array['to'],
            $array['created_at'],
            $array['updated_at']
        );
    }
}