<?php

declare(strict_types=1);

namespace App\Domain\Thing;

use App\Domain\Enums\FaultLevel;
use JsonSerializable;

readonly class Thing implements JsonSerializable
{
    public function __construct(
        private int $id,
        private string $name,
        private string $shortDescription,
        private string $description,
        private ?string $image,
        private ?string $url,
        private FaultLevel $faultLevel,
        private int $from,
        private ?int $to,
        private int $createdAt,
        private int $updatedAt,
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

    public function getShortDescription(): string
    {
        return $this->shortDescription;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getUrlHost(): string
    {
        return parse_url($this->url, PHP_URL_HOST);
    }

    public function getFaultLevel(): FaultLevel
    {
        return $this->faultLevel;
    }

    public function getFrom(): int
    {
        return $this->from;
    }

    public function getTo(): ?int
    {
        return $this->to;
    }

    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): int
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
            'image' => $this->image,
            'url' => $this->url,
            'fault_level' => $this->faultLevel,
            'from' => $this->from,
            'to' => $this->to,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
