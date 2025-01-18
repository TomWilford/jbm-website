<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use App\Infrastructure\Persistence\RepositoryInterface;

interface SeedInterface
{
    public function getName(): string;

    public function getRepository(): RepositoryInterface;

    public function getData(): array;
}
