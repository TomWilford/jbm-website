<?php

declare(strict_types=1);

namespace App\Module\Album\Domain;

use App\Common\Domain\ResolveValueTrait;
use App\Common\Domain\Unchanged;
use JsonSerializable;
use TomWilford\SlimSqids\HasSqidablePropertyTrait;
use TomWilford\SlimSqids\SqidableProperty;

class Album implements JsonSerializable
{
    use ResolveValueTrait;
    use HasSqidablePropertyTrait;

    public function __construct(
        #[SqidableProperty]
        private readonly ?int $id,
        private readonly string $name,
        private readonly Camera $camera,
        private readonly string $location,
        private readonly string $date,
        private readonly ?int $createdAt = null,
        private readonly ?int $updatedAt = null,
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCamera(): Camera
    {
        return $this->camera;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getCreatedAt(): ?int
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?int
    {
        return $this->updatedAt;
    }

    /**
     * @return array{
     *     id: ?string,
     *     name: string,
     *     camera: string,
     *     location: string,
     *     date: string,
     *     createdAt: ?int,
     *     updatedAt: ?int
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getSqid(),
            'name' => $this->getName(),
            'camera' => $this->getCamera()->value,
            'location' => $this->getLocation(),
            'date' => $this->getDate(),
            'createdAt' => $this->getCreatedAt(),
            'updatedAt' => $this->getUpdatedAt(),
        ];
    }

    public function cloneWith(
        Unchanged|int $id = Unchanged::VALUE,
        Unchanged|string $name = Unchanged::VALUE,
        Unchanged|Camera $camera = Unchanged::VALUE,
        Unchanged|string $location = Unchanged::VALUE,
        Unchanged|string $date = Unchanged::VALUE,
        Unchanged|int|null $createdAt = Unchanged::VALUE,
        Unchanged|int|null $updatedAt = Unchanged::VALUE,
    ): self {
        return new self(
            $this->resolveValue($id, $this->id),
            $this->resolveValue($name, $this->name),
            $this->resolveValue($camera, $this->camera),
            $this->resolveValue($location, $this->location),
            $this->resolveValue($date, $this->date),
            $this->resolveValue($createdAt, $this->createdAt),
            $this->resolveValue($updatedAt, $this->updatedAt),
        );
    }
}
