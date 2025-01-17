<?php

declare(strict_types=1);

namespace App\Domain\Thing;

use App\Domain\Thing\Enum\FaultLevel;
use App\Infrastructure\Enum\Unchanged;
use JsonSerializable;

readonly class Thing implements JsonSerializable
{
    public function __construct(
        private ?int $id,
        private string $name,
        private string $shortDescription,
        private string $description,
        private bool $featured,
        private FaultLevel $faultLevel,
        private int $activeFrom,
        private ?int $activeTo = null,
        private ?string $url = null,
        private ?int $createdAt = null,
        private ?int $updatedAt = null
    ) {
        //
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getShortDescription(): string
    {
        return $this->shortDescription;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getFeatured(): bool
    {
        return $this->featured;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getUrlHost(): ?string
    {
        if (!$this->url) {
            return null;
        }

        $parsedUrl = parse_url($this->url);

        if (!$parsedUrl) {
            return null;
        }

        return array_key_exists('host', $parsedUrl) ? $parsedUrl['host'] : null;
    }

    public function getFaultLevel(): FaultLevel
    {
        return $this->faultLevel;
    }

    public function getActiveFrom(): int
    {
        return $this->activeFrom;
    }

    public function getActiveTo(): ?int
    {
        return $this->activeTo;
    }

    public function getCreatedAt(): ?int
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?int
    {
        return $this->updatedAt;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'short_description' => $this->shortDescription,
            'description' => $this->description,
            'featured' => $this->featured,
            'url' => $this->url,
            'fault_level' => $this->faultLevel,
            'active_from' => $this->activeFrom,
            'active_to' => $this->activeTo,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }

    public function cloneWith(
        mixed $id = Unchanged::VALUE,
        mixed $name = Unchanged::VALUE,
        mixed $shortDescription = Unchanged::VALUE,
        mixed $description = Unchanged::VALUE,
        mixed $featured = Unchanged::VALUE,
        mixed $faultLevel = Unchanged::VALUE,
        mixed $activeFrom = Unchanged::VALUE,
        mixed $activeTo = Unchanged::VALUE,
        mixed $url = Unchanged::VALUE,
        mixed $createdAt = Unchanged::VALUE,
        mixed $updatedAt = Unchanged::VALUE
    ): Thing {
        return new self(
            $id === Unchanged::VALUE ? $this->id : $id,
            $name === Unchanged::VALUE ? $this->name : $name,
            $shortDescription === Unchanged::VALUE ? $this->shortDescription : $shortDescription,
            $description === Unchanged::VALUE ? $this->description : $description,
            $featured === Unchanged::VALUE ? $this->featured : $featured,
            $faultLevel === Unchanged::VALUE ? $this->faultLevel : $faultLevel,
            $activeFrom === Unchanged::VALUE ? $this->activeFrom : $activeFrom,
            $activeTo === Unchanged::VALUE ? $this->activeTo : $activeTo,
            $url === Unchanged::VALUE ? $this->url : $url,
            $createdAt === Unchanged::VALUE ? $this->createdAt : $createdAt,
            $updatedAt === Unchanged::VALUE ? $this->updatedAt : $updatedAt
        );
    }
}
