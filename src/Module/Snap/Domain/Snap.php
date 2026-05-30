<?php

declare(strict_types=1);

namespace App\Module\Snap\Domain;

use App\Common\Domain\ResolveValueTrait;
use App\Common\Domain\Unchanged;
use JsonSerializable;
use PhpCommonEnums\MimeType\Enumeration\MimeTypeEnum;
use TomWilford\SlimSqids\HasSqidablePropertyTrait;
use TomWilford\SlimSqids\SqidableProperty;

class Snap implements JsonSerializable
{
    use ResolveValueTrait;
    use HasSqidablePropertyTrait;

    public function __construct(
        #[SqidableProperty]
        private readonly ?int $id,
        #[SqidableProperty]
        private readonly int $albumId,
        private readonly string $image,
        private readonly MimeTypeEnum $mimeType,
        private readonly Orientation $orientation,
        private readonly ?int $createdAt = null,
        private readonly ?int $updatedAt = null,
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAlbumId(): int
    {
        return $this->albumId;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function getMimeType(): MimeTypeEnum
    {
        return $this->mimeType;
    }

    public function getCreatedAt(): ?int
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?int
    {
        return $this->updatedAt;
    }

    public function getOrientation(): Orientation
    {
        return $this->orientation;
    }

    /**
     * @return array{
     *     id: ?string,
     *     album_id: string,
     *     mime_type: string,
     *     orientation: string,
     *     created_at: ?int,
     *     updated_at: ?int
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getSqid(),
            'album_id' => $this->getAllSqids()['albumId'],
            'mime_type' => $this->getMimeType()->value,
            'orientation' => $this->getOrientation()->value,
            'created_at' => $this->getCreatedAt(),
            'updated_at' => $this->getUpdatedAt(),
        ];
    }

    public function cloneWith(
        Unchanged|int|null $id = Unchanged::VALUE,
        Unchanged|int $albumId = Unchanged::VALUE,
        Unchanged|string $image = Unchanged::VALUE,
        Unchanged|MimeTypeEnum $mimeType = Unchanged::VALUE,
        Unchanged|Orientation $orientation = Unchanged::VALUE,
        Unchanged|int|null $createdAt = Unchanged::VALUE,
        Unchanged|int|null $updatedAt = Unchanged::VALUE,
    ): self {
        return new self(
            $this->resolveValue($id, $this->id),
            $this->resolveValue($albumId, $this->albumId),
            $this->resolveValue($image, $this->image),
            $this->resolveValue($mimeType, $this->mimeType),
            $this->resolveValue($orientation, $this->orientation),
            $this->resolveValue($createdAt, $this->createdAt),
            $this->resolveValue($updatedAt, $this->updatedAt),
        );
    }
}
