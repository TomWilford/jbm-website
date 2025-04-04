<?php

declare(strict_types=1);

namespace App\Module\Bit\Data;

use App\Common\Enum\Unchanged;
use App\Domain\Service\Updater\ResolveValueTrait;
use App\Module\Bit\Enum\Language;
use JsonSerializable;
use TomWilford\SlimSqids\HasSqidablePropertyTrait;
use TomWilford\SlimSqids\SqidableProperty;

class Bit implements JsonSerializable
{
    use ResolveValueTrait;
    use HasSqidablePropertyTrait;

    public function __construct(
        #[SqidableProperty]
        private readonly ?int $id,
        private readonly string $name,
        private readonly string $code,
        private readonly Language $language,
        private readonly ?string $description = null,
        private readonly ?string $returns = null,
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

    public function getReturns(): ?string
    {
        return $this->returns;
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
            'id' => $this->getSqid(),
            'name' => $this->name,
            'code' => $this->code,
            'language' => $this->language->name,
            'description' => $this->description,
            'returns' => $this->returns,
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
        mixed $returns = Unchanged::VALUE,
        mixed $createdAt = Unchanged::VALUE,
        mixed $updatedAt = Unchanged::VALUE,
    ): self {
        return new self(
            $this->resolveValue($id, $this->id),
            $this->resolveValue($name, $this->name),
            $this->resolveValue($code, $this->code),
            $this->resolveValue($language, $this->language),
            $this->resolveValue($description, $this->description),
            $this->resolveValue($returns, $this->returns),
            $this->resolveValue($createdAt, $this->createdAt),
            $this->resolveValue($updatedAt, $this->updatedAt)
        );
    }
}
