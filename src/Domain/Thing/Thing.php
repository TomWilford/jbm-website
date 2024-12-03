<?php

namespace App\Domain\Thing;

use JsonSerializable;
readonly class Thing implements JsonSerializable
{
    public function __construct(
        private int    $id,
        private string $name,
        private string $description,
        private string $shortDescription,
        private ?string $image,
        private ?string $url,
    )
    {
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

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'shortDescription' => $this->shortDescription,
            'image' => $this->image,
            'url' => $this->url,
        ];
    }
}