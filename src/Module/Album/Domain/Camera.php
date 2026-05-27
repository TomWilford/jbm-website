<?php

declare(strict_types=1);

namespace App\Module\Album\Domain;

enum Camera: string
{
    case YASHICA_635 = 'yashica635';
    case OLYMPUS_PEN = 'olympusPen';
    case OLYMPUS_35RC = 'olympus35RC';

    public function getDisplayName(): string
    {
        return match ($this) {
            self::OLYMPUS_35RC => 'Olympus 35RC',
            default => ucwords(strtolower(str_replace('_', ' ', $this->name))),
        };
    }
}
