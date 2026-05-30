<?php

declare(strict_types=1);

namespace App\Module\Bit\Application\Service;

use App\Application\Service\UpdaterInterface;
use App\Common\Domain\Unchanged;
use App\Module\Bit\Domain\Bit;
use App\Module\Bit\Domain\Language;
use App\Module\Bit\Infrastructure\BitRepository;
use Doctrine\DBAL\Exception;
use InvalidArgumentException;

class UpdateBit implements UpdaterInterface
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
