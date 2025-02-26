<?php

declare(strict_types=1);

namespace App\Domain\Bit\Service\Update;

use App\Domain\Bit\Bit;
use App\Domain\Bit\Enum\Language;
use App\Domain\Bit\Repository\BitRepository;
use App\Infrastructure\Enum\Unchanged;
use App\Infrastructure\Service\Updater\UpdaterInterface;
use Doctrine\DBAL\Exception;
use InvalidArgumentException;

class BitUpdater implements UpdaterInterface
{
    public function __construct(protected BitRepository $repository)
    {
    }

    /**
     * @param array{
     *     name: string,
     *     code: string,
     *     language: string,
     *     description: string,
     *     returns: string,
     * }|array<string, mixed> $data
     * @param object $entity
     *
     * @throws Exception
     */
    public function updateFromArray(array $data, object $entity): Bit
    {
        if (!$entity instanceof Bit) {
            throw new InvalidArgumentException('Entity must be instance of ' . Bit::class);
        }
        $bit = $entity->cloneWith(
            name: ($data['name'] === '') ? Unchanged::VALUE : $data['name'],
            code: ($data['code'] === '') ? Unchanged::VALUE : $data['code'],
            language: $this->resolveLanguageValue($data['language']),
            description: $this->resolveNullableString($data['description']),
            returns: $this->resolveNullableString($data['returns'])
        );

        return $this->repository->update($bit);
    }

    private function resolveLanguageValue(string $language): Unchanged|Language
    {
        return $language === '' ? Unchanged::VALUE : Language::{strtoupper($language)};
    }

    private function resolveNullableString(string $string): Unchanged|string|null
    {
        return ($string !== 'null')
            ? ($string === '' ? Unchanged::VALUE : $string)
            : null;
    }
}
