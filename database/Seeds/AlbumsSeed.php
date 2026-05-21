<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use App\Infrastructure\Persistence\RepositoryInterface;
use App\Module\Album\Domain\Album;
use App\Module\Album\Domain\Camera;
use App\Module\Album\Infrastructure\AlbumRepository;

class AlbumsSeed implements SeedInterface
{
    public function __construct(private AlbumRepository $repository)
    {
    }

    public function getName(): string
    {
        return 'Albums';
    }

    public function getRepository(): RepositoryInterface
    {
        return $this->repository;
    }

    public function getData(): array
    {
        return [
            new Album(
                id: null,
                name: 'Netherlands Bikepacking, 2026',
                camera: Camera::OLYMPUS_35RC,
                location: 'Netherlands',
                date: '2026-04-25 - 2026-04-30',
            )
        ];
    }
}
