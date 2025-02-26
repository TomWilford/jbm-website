<?php

declare(strict_types=1);

namespace App\Domain\Bit\Service\Create;

use App\Domain\Bit\Bit;
use App\Domain\Bit\Enum\Language;
use App\Domain\Bit\Repository\BitRepository;
use App\Infrastructure\Service\Creator\CreatorInterface;
use Doctrine\DBAL\Exception;

readonly class BitCreator implements CreatorInterface
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
     *     returns: string
     * }|array<string, mixed> $data
     *
     * @throws Exception
     */
    public function createFromArray(array $data): Bit
    {
        $language = $this->normaliseLanguageInput($data['language']);
        $description = $this->normaliseNullableInput($data['description']);
        $returns = $this->normaliseNullableInput($data['returns']);
        $bit = new Bit(
            id: null,
            name: $data['name'],
            code: $data['code'],
            language: Language::{$language},
            description: $description,
            returns: $returns
        );

        return $this->repository->store($bit);
    }

    private function normaliseLanguageInput(string $language): string
    {
        return strtoupper($language);
    }

    private function normaliseNullableInput(string $input): ?string
    {
        return $input === '' ? null : $input;
    }
}
