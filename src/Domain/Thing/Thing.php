<?php

declare(strict_types=1);

namespace App\Domain\Thing;

use JsonSerializable;

readonly class Thing implements JsonSerializable
{
    public function __construct(
        private int $id,
        private string $name,
        private string $description,
        private string $shortDescription,
        private ?string $image,
        private ?string $url,
        private string $createdAt,
        private string $updatedAt,
    ) {
        //
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getShortDescription(): string
    {
        return $this->shortDescription;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'short_description' => $this->shortDescription,
            'image' => $this->image,
            'url' => $this->url,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
