<?php

declare(strict_types=1);

namespace App\Domain\Thing;

use App\Domain\Thing\Enum\FaultLevel;
use App\Infrastructure\Enum\Unchanged;
use App\Infrastructure\Service\Updater\ResolveValueTrait;
use JsonSerializable;

readonly class Thing implements JsonSerializable
{
    use ResolveValueTrait;

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
            $this->resolveValue($id, $this->id),
            $this->resolveValue($name, $this->name),
            $this->resolveValue($shortDescription, $this->shortDescription),
            $this->resolveValue($description, $this->description),
            $this->resolveValue($featured, $this->featured),
            $this->resolveValue($faultLevel, $this->faultLevel),
            $this->resolveValue($activeFrom, $this->activeFrom),
            $this->resolveValue($activeTo, $this->activeTo),
            $this->resolveValue($url, $this->url),
            $this->resolveValue($createdAt, $this->createdAt),
            $this->resolveValue($updatedAt, $this->updatedAt)
        );
    }
}
