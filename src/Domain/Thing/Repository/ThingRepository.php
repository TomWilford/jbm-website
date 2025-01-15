<?php

declare(strict_types=1);

namespace App\Domain\Thing\Repository;

use App\Domain\Exception\DomainRecordNotFoundException;
use App\Domain\Thing\Enum\FaultLevel;
use App\Domain\Thing\Thing;
use App\Infrastructure\Persistence\Repository;
use Doctrine\DBAL\Exception;

final class ThingRepository extends Repository
{
    /**
     * @throws Exception
     */
    public function store(Thing $thing): Thing
    {
        $qb = $this->getQueryBuilder();

        $qb->insert('things')
            ->values([
                'name' => ':name',
                'short_description' => ':short_description',
                'description' => ':description',
                'featured' => ':featured',
                'fault_level' => ':fault_level',
                'active_from' => ':active_from',
                'active_to' => ':active_to',
                'url' => ':url',
                'created_at' => ':created_at',
                'updated_at' => ':updated_at',
            ])
            ->setParameters([
                'name' => $thing->getName(),
                'short_description' => $thing->getShortDescription(),
                'description' => $thing->getDescription(),
                'featured' => $thing->getFeatured(),
                'fault_level' => $thing->getFaultLevel()->value,
                'active_from' => $thing->getActiveFrom(),
                'active_to' => $thing->getActiveTo(),
                'url' => $thing->getUrl(),
                'created_at' => (new \DateTimeImmutable())->getTimestamp(),
                'updated_at' => (new \DateTimeImmutable())->getTimestamp(),
        ]);

        $qb->executeStatement();

        $id = (int)$this->connection->lastInsertId();

        return $this->ofId($id);
    }

    /** @return Thing[]
     * @throws Exception
     */
    public function all(): iterable
    {
        $result = $this->fetch('things');

        return $this->arrayToObjects($result);
    }

    /**
     * @throws DomainRecordNotFoundException
     * @throws Exception
     */
    public function ofId(int $id): Thing
    {
        $result = $this->fetch('things', ['id' => $id]);

        if (!$result) {
            throw new DomainRecordNotFoundException('Thing not found');
        }

        return $this->arrayToObject($result[0]);
    }

    /**
     * @throws Exception
     */
    public function update(Thing $thing): Thing
    {
        $qb = $this->getQueryBuilder();

        $qb->update('things')
            ->set('name', ':name')
            ->set('short_description', ':short_description')
            ->set('description', ':description')
            ->set('featured', ':featured')
            ->set('fault_level', ':fault_level')
            ->set('active_from', ':active_from')
            ->set('active_to', ':active_to')
            ->set('url', ':url')
            ->set('updated_at', ':updated_at')
            ->where('id = :id')
        ->setParameters([
            'name' => $thing->getName(),
            'short_description' => $thing->getShortDescription(),
            'description' => $thing->getDescription(),
            'featured' => $thing->getFeatured(),
            'fault_level' => $thing->getFaultLevel()->value,
            'active_from' => $thing->getActiveFrom(),
            'active_to' => $thing->getActiveTo(),
            'url' => $thing->getUrl(),
            'updated_at' => (new \DateTimeImmutable())->getTimestamp(),
            'id' => $thing->getId(),
        ]);

        $qb->executeStatement();

        return $this->ofId($thing->getId());
    }

    /**
     * @throws Exception
     */
    public function destroy(Thing $thing): void
    {
        $qb = $this->getQueryBuilder();

        $qb->delete('things')
            ->where('id = :id')
            ->setParameter('id', $thing->getId());

        $qb->executeStatement();
    }

    /** @return Thing[]
     * @throws Exception
     */
    public function recent(): iterable
    {
        $result = $this->fetch('things', ['featured' => 1], 3, 'active_from', 'DESC');

        return $this->arrayToObjects($result);
    }

    /**
     * @param array{
     *     id: int,
     *     name: string,
     *     short_description: string,
     *     description: string,
     *     featured: bool,
     *     fault_level: string,
     *     active_from: int,
     *     active_to: ?int,
     *     url: ?string,
     *     created_at: int,
     *     updated_at: int
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
