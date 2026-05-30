<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use App\Database\Seeds\SeedInterface;
use App\Infrastructure\Persistence\RepositoryInterface;
use App\Module\Snap\Domain\Orientation;
use App\Module\Snap\Domain\Snap;
use App\Module\Snap\Infrastructure\SnapRepository;
use Nyholm\Psr7\Stream;
use PhpCommonEnums\MimeType\Enumeration\MimeTypeEnum;
use Psr\Http\Message\StreamInterface;

readonly class SnapSeed implements SeedInterface
{
    public function __construct(private SnapRepository $repository)
    {
    }

    public function getName(): string
    {
        return 'Snaps';
    }

    public function getRepository(): RepositoryInterface
    {
        return $this->repository;
    }

    /**
     * @return array<Snap>
     */
    public function getData(): array
    {
        return [
            new Snap(
                id: null,
                albumId: 1,
                image: file_get_contents(dirname(__DIR__) . '/Seeds/assets/snap-01.webp'),
                mimeType: MimeTypeEnum::ImageWebp,
                orientation: Orientation::PORTRAIT
            )
        ];
    }
}
