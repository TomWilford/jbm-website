<?php

declare(strict_types=1);

namespace App\Domain\Bit\Repository;

use App\Domain\Bit\Bit;
use App\Domain\Bit\Enum\Language;
use App\Domain\Exception\DomainRecordNotFoundException;
use App\Infrastructure\Persistence\Repository;
use Doctrine\DBAL\Exception;

class BitRepository extends Repository
{
    /**
     * @throws Exception
     */
    public function store(object $entity): Bit
    {
        if (!$entity instanceof Bit) {
            throw new \InvalidArgumentException('Entity must be instance of ' . Bit::class);
        }

        $qb = $this->getQueryBuilder();

        $qb->insert('bits')
            ->values([
                'name' => ':name',
                'code' => ':code',
                'language' => ':language',
                'description' => ':description',
                'returns' => ':returns',
                'created_at' => ':created_at',
                'updated_at' => ':updated_at',
            ])
            ->setParameters([
                'name' => $entity->getName(),
                'code' => $entity->getCode(),
                'language' => $entity->getLanguage()->name,
                'description' => $entity->getDescription(),
                'returns' => $entity->getReturns(),
                'created_at' => (new \DateTimeImmutable())->getTimestamp(),
                'updated_at' => (new \DateTimeImmutable())->getTimestamp(),
            ]);

        $qb->executeStatement();

        $id = (int)$this->connection->lastInsertId();

        return $this->ofId($id);
    }

    /**
     * @return Bit[]
     * @throws Exception
     */
    public function all(): iterable
    {
        $result = $this->fetch('bits');

        return $this->arrayToObjects($result);
    }

    /**
     * @throws DomainRecordNotFoundException
     * @throws Exception
     */
    public function ofId(int $id): Bit
    {
        $result = $this->fetch('bits', ['id' => $id]);

        if (!$result) {
            throw new DomainRecordNotFoundException('Bit not found');
        }

        return $this->arrayToObject($result[0]);
    }

    /**
     * @throws Exception
     */
    public function update(object $entity): Bit
    {
        if (!$entity instanceof Bit) {
            throw new \InvalidArgumentException('Entity must be instance of ' . Bit::class);
        }

        if (is_null($entity->getId())) {
            throw new DomainRecordNotFoundException('Cannot update provided Bit');
        }

        $qb = $this->getQueryBuilder();

        $qb->update('bits')
            ->set('name', ':name')
            ->set('code', ':code')
            ->set('language', ':language')
            ->set('description', ':description')
            ->set('returns', ':returns')
            ->set('updated_at', ':updated_at')
            ->where('id = :id')
        ->setParameters([
            'name' => $entity->getName(),
            'code' => $entity->getCode(),
            'language' => $entity->getLanguage()->name,
            'description' => $entity->getDescription(),
            'returns' => $entity->getReturns(),
            'updated_at' => (new \DateTimeImmutable())->getTimestamp(),
            'id' => $entity->getId()
        ]);

        $qb->executeStatement();

        return $this->ofId($entity->getId());
    }

    /**
     * @throws Exception
     */
    public function destroy(object $entity): void
    {
        if (!$entity instanceof Bit || is_null($entity->getId())) {
            throw new \InvalidArgumentException('Entity must be instance of ' . Bit::class);
        }

        try {
            $this->ofId($entity->getId());
        } catch (DomainRecordNotFoundException $exception) {
            throw new DomainRecordNotFoundException('Bit not found');
        }

        $qb = $this->getQueryBuilder();

        $qb->delete('bits')
            ->where('id = :id')
            ->setParameter('id', $entity->getId());

        $qb->executeStatement();
    }

    /**
     * @param array{
     *     id: int,
     *     name: string,
     *     code: string,
     *     language: string,
     *     description: ?string,
     *     returns: ?string,
     *     created_at: int,
     *     updated_at: int
     * }|array<string, mixed> $array
     */
    public function arrayToObject(array $array): Bit
    {
        return new Bit(
            $array['id'],
            $array['name'],
            $array['code'],
            Language::{$array['language']},
            $array['description'],
            $array['returns'],
            $array['created_at'],
            $array['updated_at'],
        );
    }
}
