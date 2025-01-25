<?php

declare(strict_types=1);

namespace App\Domain\Bit\Enum;

enum Language
{
    case PHP;
    case JS;
    case MIXED;

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn(self $item): string => $item->name, self::cases());
    }
}
