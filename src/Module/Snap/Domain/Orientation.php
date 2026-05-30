<?php

declare(strict_types=1);

namespace App\Module\Snap\Domain;

enum Orientation: string
{
    case PORTRAIT = 'portrait';
    case LANDSCAPE = 'landscape';
    case SQUARE = 'square';
}
