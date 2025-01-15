<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\Creator;

interface CreatorInterface
{
    /**
     * @param array{mixed} $data
     */
    public function createFromArray(array $data): object;
}
