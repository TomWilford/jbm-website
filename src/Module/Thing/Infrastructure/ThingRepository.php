<?php

declare(strict_types=1);

namespace App\Module\Thing\Infrastructure;

use App\Domain\Exception\DomainRecordNotFoundException;
use App\Infrastructure\Persistence\Repository;
use App\Module\Thing\Data\Thing;
use App\Module\Thing\Enum\FaultLevel;
use Doctrine\DBAL\Exception;
use InvalidArgumentException;

class ThingRepository extends Repository
{
    /**
     * @throws Exception
     */
    public function store(object $entity): Thing
    {
        if (!$entity instanceof Thing) {
            throw new InvalidArgumentException('Entity must be an instance of ' . Thing::class);
        }

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
                'name' => $entity->getName(),
                'short_description' => $entity->getShortDescription(),
                'description' => $entity->getDescription(),
                'featured' => $entity->getFeatured(),
                'fault_level' => $entity->getFaultLevel()->value,
                'active_from' => $entity->getActiveFrom(),
                'active_to' => $entity->getActiveTo(),
                'url' => $entity->getUrl(),
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
    public function update(object $entity): Thing
    {
        if (!$entity instanceof Thing) {
            throw new InvalidArgumentException('Entity must be an instance of ' . Thing::class);
        }

        if (is_null($entity->getId())) {
            throw new DomainRecordNotFoundException('Cannot update provided Thing');
        }

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
            'name' => $entity->getName(),
            'short_description' => $entity->getShortDescription(),
            'description' => $entity->getDescription(),
            'featured' => $entity->getFeatured(),
            'fault_level' => $entity->getFaultLevel()->value,
            'active_from' => $entity->getActiveFrom(),
            'active_to' => $entity->getActiveTo(),
            'url' => $entity->getUrl(),
            'updated_at' => (new \DateTimeImmutable())->getTimestamp(),
            'id' => $entity->getId(),
        ]);

        $qb->executeStatement();

        return $this->ofId($entity->getId());
    }

    /**
     * @throws Exception
     */
    public function destroy(object $entity): void
    {
        if (!$entity instanceof Thing || is_null($entity->getId())) {
            throw new InvalidArgumentException('Entity must be an instance of ' . Thing::class);
        }

        try {
            $this->ofId($entity->getId());
        } catch (DomainRecordNotFoundException $exception) {
            throw new DomainRecordNotFoundException('Thing not found');
        }

        $qb = $this->getQueryBuilder();

        $qb->delete('things')
            ->where('id = :id')
            ->setParameter('id', $entity->getId());

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
     * }|array<string, mixed> $array
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
