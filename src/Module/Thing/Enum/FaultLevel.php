<?php

declare(strict_types=1);

namespace App\Module\Thing\Enum;

enum FaultLevel: string
{
    case ALL = 'all';
    case MOSTLY = 'most';
    case PARTLY = 'part';

    /**
     * @return array{string}
     */
    public static function values(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }
}
