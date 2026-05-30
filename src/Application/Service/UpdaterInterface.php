<?php

declare(strict_types=1);

namespace App\Application\Service;

interface UpdaterInterface
{
    /**
     * @param array<string, mixed> $data
     * @param object $entity
     */
    public function updateFromArray(array $data, object $entity): object;
}
