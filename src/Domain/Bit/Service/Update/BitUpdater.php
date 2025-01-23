<?php

declare(strict_types=1);

namespace App\Domain\Bit\Service\Update;

use App\Domain\Bit\Bit;
use App\Domain\Bit\Enum\Language;
use App\Domain\Bit\Repository\BitRepository;
use App\Infrastructure\Enum\Unchanged;
use App\Infrastructure\Service\Updater\UpdaterInterface;
use Doctrine\DBAL\Exception;

class BitUpdater implements UpdaterInterface
{
    public function __construct(protected BitRepository $repository)
    {
        //
    }

    /**
     * @param array{
     *     name: string,
     *     code: string,
     *     language: string,
     *     description: string
     * }|array<string, mixed> $data
     * @throws Exception
     */
    public function updateFromArray(array $data, object $entity): Bit
    {
        if (!$entity instanceof Bit) {
            throw new \InvalidArgumentException('Entity must be instance of ' . Bit::class);
        }
        $bit = $entity->cloneWith(
            name: ($data['name'] === '') ? Unchanged::VALUE : $data['name'],
            code: ($data['code'] === '') ? Unchanged::VALUE : $data['code'],
            language: $this->resolveLanguageValue($data['language']),
            description: $this->resolveDescriptionValue($data['description'])
        );

        return $this->repository->update($bit);
    }

    private function resolveLanguageValue(string $language): Unchanged|Language
    {
        return $language === '' ? Unchanged::VALUE : Language::{strtoupper($language)};
    }

    private function resolveDescriptionValue(string $description): Unchanged|string|null
    {
        return ($description !== 'null')
            ? ($description === '' ? Unchanged::VALUE : $description)
            : null;
    }
}
