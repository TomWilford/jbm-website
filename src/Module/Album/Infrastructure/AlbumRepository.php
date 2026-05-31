<?php

declare(strict_types=1);

namespace App\Module\Album\Infrastructure;

use App\Infrastructure\Exception\DomainRecordNotFoundException;
use App\Infrastructure\Persistence\Repository;
use App\Module\Album\Domain\Album;
use App\Module\Album\Domain\Camera;
use App\Module\Thing\Domain\Thing;
use DateTimeImmutable;
use Doctrine\DBAL\Exception;
use InvalidArgumentException;

class AlbumRepository extends Repository
{
    public function store(object $entity): Album
    {
        if (!$entity instanceof Album) {
            throw new InvalidArgumentException('Entity must be an instance of ' . Album::class);
        }

        $qb = $this->getQueryBuilder();
        $now = (new DateTimeImmutable())->getTimestamp();

        $qb->insert('albums')
            ->values([
                'name' => ':name',
                'camera' => ':camera',
                'location' => ':location',
                'date' => ':date',
                'sort_date' => ':sort_date',
                'created_at' => ':created_at',
                'updated_at' => ':updated_at',
            ])
            ->setParameters([
                'name' => $entity->getName(),
                'camera' => $entity->getCamera()->value,
                'location' => $entity->getLocation(),
                'date' => $entity->getDate(),
                'sort_date' => $entity->getSortDate(),
                'created_at' => $now,
                'updated_at' => $now,
            ]);

        $qb->executeStatement();

        $id = (int)$this->connection->lastInsertId();

        return $this->ofId($id);
    }

    /**
     * {@inheritDoc}
     */
    public function all(): iterable
    {
        $result = $this->fetch(table: 'albums', orderBy: 'sort_date', orderDirection: 'DESC');

        return $this->arrayToObjects($result);
    }

    /**
     * @throws Exception
     *
     * @return Thing[]
     */
    public function recent(): iterable
    {
        $result = $this->fetch(table: 'albums', limit: 3, orderBy: 'sort_date', orderDirection: 'DESC');

        return $this->arrayToObjects($result);
    }

    public function ofId(int $id): Album
    {
        $result = $this->fetch('albums', ['id' => $id]);

        if (!$result) {
            throw new DomainRecordNotFoundException('Album not found');
        }

        return $this->arrayToObject($result[0]);
    }

    public function update(object $entity): Album
    {
        if (!$entity instanceof Album) {
            throw new InvalidArgumentException('Entity must be an instance of ' . Album::class);
        }

        $qb = $this->getQueryBuilder();

        $qb->update('albums')
            ->set('name', ':name')
            ->set('camera', ':camera')
            ->set('location', ':location')
            ->set('date', ':date')
            ->set('sort_date', ':sort_date')
            ->set('updated_at', ':updated_at')
            ->where('id = :id')
            ->setParameters([
                'name' => $entity->getName(),
                'camera' => $entity->getCamera()->value,
                'location' => $entity->getLocation(),
                'date' => $entity->getDate(),
                'sort_date' => $entity->getSortDate(),
                'updated_at' => new DateTimeImmutable()->getTimestamp(),
                'id' => $entity->getId(),
            ]);

        $qb->executeStatement();

        return $this->ofId((int)$entity->getId());
    }

    public function destroy(object $entity): void
    {
        if (!$entity instanceof Album) {
            throw new InvalidArgumentException('Entity must be an instance of ' . Album::class);
        }

        $this->ofId((int)$entity->getId());

        $qb = $this->getQueryBuilder();
        $qb->delete('albums')
            ->where('id = :id')
            ->setParameter('id', $entity->getId());

        $qb->executeStatement();
    }

    /**
     * @param array{
     *     id: int,
     *     name: string,
     *     camera: string,
     *     location: string,
     *     date: string,
     *     sort_date: int,
     *     created_at: int,
     *     updated_at: int
     * }|array<string, mixed> $array
     *
     * @throws DomainRecordNotFoundException
     */
    public function arrayToObject(array $array): Album
    {
        return new Album(
            (int)$array['id'],
            $array['name'],
            Camera::from($array['camera']),
            $array['location'],
            $array['date'],
            $array['sort_date'],
            $array['created_at'] ?? null,
            $array['updated_at'] ?? null
        );
    }
}
