<?php

declare(strict_types=1);

namespace App\Common\Domain;

trait ResolveValueTrait
{
    private function resolveValue(mixed $value, mixed $currentValue): mixed
    {
        return $value === Unchanged::VALUE ? $currentValue : $value;
    }
}
