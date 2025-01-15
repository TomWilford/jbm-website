<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\Updater;

interface UpdaterInterface
{
    /**
     * @param array{mixed} $data
     */
    public function updateFromArray(array $data, object $entity): object;
}