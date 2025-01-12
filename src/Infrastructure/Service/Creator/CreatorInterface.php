<?php

namespace App\Infrastructure\Service\Creator;

interface CreatorInterface
{
    /**
     * @param array{mixed} $data
     */
    public function create(array $data): object;
}
