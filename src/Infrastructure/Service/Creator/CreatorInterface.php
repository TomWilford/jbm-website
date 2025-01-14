<?php

namespace App\Infrastructure\Service\Creator;

interface CreatorInterface
{
    /**
     * @param array{mixed} $data
     */
    public function createFromArray(array $data): object;
}
