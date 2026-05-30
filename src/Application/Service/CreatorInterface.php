<?php

declare(strict_types=1);

namespace App\Application\Service;

interface CreatorInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function createFromArray(array $data): object;
}
