<?php

declare(strict_types=1);

namespace App\Domain\Thing;

use App\Domain\Exception\DomainRecordNotFoundException;
use App\Domain\Thing\Enum\FaultLevel;
use App\Infrastructure\Persistence\Repository;

class ThingRepository extends Repository
{
    public function store(Thing $thing): void
    {
        $this->database->query(
            <<<SQL
                INSERT INTO things (
                    name,
                    short_description,
                    description,
                    featured,
                    fault_level,
                    active_from,
                    active_to,
                    url,
                    created_at,
                    updated_at
                ) VALUES (
                    :name,
                    :shortDescription,
                    :description,
                    :featured,
                    :faultLevel,
                    :activeFrom,
                    :activeTo,
                    :url,
                    unixepoch('now'),
                    unixepoch('now')
                )
            SQL,
            [
                "name" => $thing->getName(),
                "shortDescription" => $thing->getShortDescription(),
                "description" => $thing->getDescription(),
                "featured" => $thing->getFeatured(),
                "faultLevel" => $thing->getFaultLevel()->value,
                "activeFrom" => $thing->getActiveFrom(),
                "activeTo" => $thing->getActiveTo(),
                "url" => $thing->getUrl(),
            ]
        );
    }

    /** @return Thing[] */
    public function all(): iterable
    {
        return $this->arrayToObjects(
            $this->database->query('SELECT * FROM things')
        );
    }

    /**
     * @throws DomainRecordNotFoundException
     */
    public function ofId(int $id): Thing
    {
        $result = $this->database->query('SELECT * FROM things WHERE id = :id', ['id' => $id]);

        if (!$result) {
            throw new DomainRecordNotFoundException('Thing not found');
        }

        return $this->arrayToObject(
            $result[0]
        );
    }

    public function update(Thing $thing): void
    {
        $this->database->query(
            <<<SQL
                UPDATE things SET
                    name = :name,
                    short_description = :shortDescription,
                    description = :description,
                    featured = :featured,
                    fault_level = :faultLevel,
                    active_from = :activeFrom,
                    active_to  = :activeTo,
                    url = :url,
                    updated_at = unixepoch('now')
                WHERE 
                    id = :id
            SQL,
            [
                "name" => $thing->getName(),
                "shortDescription" => $thing->getShortDescription(),
                "description" => $thing->getDescription(),
                "featured" => $thing->getFeatured(),
                "faultLevel" => $thing->getFaultLevel(),
                "activeFrom" => $thing->getActiveFrom(),
                "activeTo" => $thing->getActiveTo(),
                "url" => $thing->getUrl(),
                "id" => $thing->getId()
            ]
        );
    }

    public function destroy(Thing $thing): void
    {
        $this->database->query(
            <<<SQL
                DELETE FROM things WHERE id = :id
            SQL,
            [
                "id" => $thing->getId()
            ]
        );
    }

    /** @return Thing[] */
    public function recent(): iterable
    {
        return $this->arrayToObjects(
            $this->database->query('SELECT * FROM things WHERE featured = 1 ORDER BY active_from DESC LIMIT 3')
        );
    }

    /**
     * @param array{
     *     "id": int,
     *     "name": string,
     *     "short_description": string,
     *     "description": string,
     *     "featured": bool,
     *     "fault_level": string,
     *     "active_from": int,
     *     "active_to": ?int,
     *     "url": ?string,
     *     "created_at": int,
     *     "updated_at": int
     * } $array
     */
    public function arrayToObject(array $array): Thing
    {
        return new Thing(
            $array['id'],
            $array['name'],
            $array['short_description'],
            $array['description'],
            (bool)$array['featured'],
            FaultLevel::from($array['fault_level']),
            $array['active_from'],
            $array['active_to'],
            $array['url'],
            $array['created_at'],
            $array['updated_at']
        );
    }
}
