<?php

declare(strict_types=1);

namespace App\Domain\Bit;

use App\Domain\Bit\Enum\Language;
use App\Infrastructure\Enum\Unchanged;
use App\Infrastructure\Service\Updater\ResolveValueTrait;
use JsonSerializable;

readonly class Bit implements JsonSerializable
{
    use ResolveValueTrait;

    public function __construct(
        private ?int $id,
        private string $name,
        private string $code,
        private Language $language,
        private ?string $description = null,
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

    public function getCode(): string
    {
        return $this->code;
    }

    public function getLanguage(): Language
    {
        return $this->language;
    }

    public function getDescription(): ?string
    {
        return $this->description;
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
            'code' => $this->code,
            'language' => $this->language,
            'description' => $this->description,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }

    public function cloneWith(
        mixed $id = Unchanged::VALUE,
        mixed $name = Unchanged::VALUE,
        mixed $code = Unchanged::VALUE,
        mixed $language = Unchanged::VALUE,
        mixed $description = Unchanged::VALUE,
        mixed $createdAt = Unchanged::VALUE,
        mixed $updatedAt = Unchanged::VALUE
    ): self {
        return new self(
            $this->resolveValue($id, $this->id),
            $this->resolveValue($name, $this->name),
            $this->resolveValue($code, $this->code),
            $this->resolveValue($language, $this->language),
            $this->resolveValue($description, $this->description),
            $this->resolveValue($createdAt, $this->createdAt),
            $this->resolveValue($updatedAt, $this->updatedAt)
        );
    }
}
