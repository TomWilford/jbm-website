<?php

namespace App\Domain\Service\Updater;

use App\Common\Enum\Unchanged;

trait ResolveValueTrait
{
    private function resolveValue(mixed $value, mixed $currentValue): mixed
    {
        return $value === Unchanged::VALUE ? $currentValue : $value;
    }
}
