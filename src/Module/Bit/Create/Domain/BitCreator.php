<?php

declare(strict_types=1);

namespace App\Module\Bit\Create\Domain;

use App\Domain\Service\Creator\CreatorInterface;
use App\Module\Bit\Data\Bit;
use App\Module\Bit\Enum\Language;
use App\Module\Bit\Infrastructure\BitRepository;
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
