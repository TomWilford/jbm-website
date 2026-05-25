<?php

declare(strict_types=1);

namespace App\Module\Snap\Infrastructure;

use App\Infrastructure\Exception\DomainRecordNotFoundException;
use App\Infrastructure\Persistence\Repository;
use App\Module\Snap\Domain\Snap;
use DateTimeImmutable;
use Doctrine\DBAL\Exception;
use InvalidArgumentException;
use PhpCommonEnums\MimeType\Enumeration\MimeTypeEnum;

class SnapRepository extends Repository
{
    /**
     * @param object $entity
     *
     * @throws Exception
     */
    public function store(object $entity): Snap
    {
        if (!$entity instanceof Snap) {
            throw new InvalidArgumentException('Entity must be an instance of ' . Snap::class);
        }

        $qb = $this->getQueryBuilder();
        $now = (new DateTimeImmutable())->getTimestamp();

        $qb->insert('snaps')
            ->values([
                'album_id' => ':album_id',
                'image' => ':image',
                'mime_type' => ':mime_type',
                'created_at' => ':created_at',
                'updated_at' => ':updated_at',
            ])
            ->setParameters([
                'album_id' => $entity->getAlbumId(),
                'image' => $entity->getImage(),
                'mime_type' => $entity->getMimeType()->value, // Backed enum string value
                'created_at' => $now,
                'updated_at' => $now,
            ]);

        $qb->executeStatement();

        $id = (int)$this->connection->lastInsertId();

        return $this->ofId($id);
    }

    /**
     * @throws Exception
     *
     * @return Snap[]
     */
    public function all(): iterable
    {
        $result = $this->fetch('snaps');

        return $this->arrayToObjects($result);
    }

    /**
     * @param int $id
     *
     * @throws DomainRecordNotFoundException
     * @throws Exception
     */
    public function ofId(int $id): Snap
    {
        $result = $this->fetch('snaps', ['id' => $id]);

        if (!$result) {
            throw new DomainRecordNotFoundException('Snap not found');
        }

        return $this->arrayToObject($result[0]);
    }

    /**
     * @param int $albumId
     *
     * @throws Exception
     *
     * @return Snap[]
     */
    public function ofAlbumId(int $albumId): iterable
    {
        $result = $this->fetch('snaps', ['album_id' => $albumId]);

        return $this->arrayToObjects($result);
    }

    /**
     * @param object $entity
     *
     * @throws Exception
     */
    public function update(object $entity): Snap
    {
        if (!$entity instanceof Snap) {
            throw new InvalidArgumentException('Entity must be an instance of ' . Snap::class);
        }

        if (is_null($entity->getId())) {
            throw new DomainRecordNotFoundException('Cannot update provided Snap');
        }

        $qb = $this->getQueryBuilder();

        $qb->update('snaps')
            ->set('album_id', ':album_id')
            ->set('image', ':image')
            ->set('mime_type', ':mime_type')
            ->set('updated_at', ':updated_at')
            ->where('id = :id')
            ->setParameters([
                'album_id' => $entity->getAlbumId(),
                'image' => $entity->getImage(),
                'mime_type' => $entity->getMimeType()->value,
                'updated_at' => (new DateTimeImmutable())->getTimestamp(),
                'id' => $entity->getId(),
            ]);

        $qb->executeStatement();

        return $this->ofId($entity->getId());
    }

    /**
     * @param object $entity
     *
     * @throws Exception
     */
    public function destroy(object $entity): void
    {
        if (!$entity instanceof Snap || is_null($entity->getId())) {
            throw new InvalidArgumentException('Entity must be an instance of ' . Snap::class);
        }

        $this->ofId($entity->getId());

        $qb = $this->getQueryBuilder();
        $qb->delete('snaps')
            ->where('id = :id')
            ->setParameter('id', $entity->getId());

        $qb->executeStatement();
    }

    /**
     * @param array{
     *     id: int,
     *     album_id: int,
     *     image: string,
     *     mime_type: string,
     *     created_at: int,
     *     updated_at: int
     * }|array<string, mixed> $array
     */
    public function arrayToObject(array $array): Snap
    {
        return new Snap(
            (int)$array['id'],
            (int)$array['album_id'],
            $array['image'],
            MimeTypeEnum::from($array['mime_type']),
            $array['created_at'] !== null ? (int)$array['created_at'] : null,
            $array['updated_at'] !== null ? (int)$array['updated_at'] : null
        );
    }
}
