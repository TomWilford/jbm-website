<?php

namespace App\Infrastructure\Service\Updater;

use App\Infrastructure\Enum\Unchanged;

trait ResolveValueTrait
{
    private function resolveValue(mixed $value, mixed $currentValue): mixed
    {
        return $value === Unchanged::VALUE ? $currentValue : $value;
    }
}
