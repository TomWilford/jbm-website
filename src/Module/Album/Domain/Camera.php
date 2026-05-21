<?php

declare(strict_types=1);

namespace App\Module\Album\Domain;

enum Camera: string
{
    case YASHICA_635 = 'yashica635';
    case OLYMPUS_PEN = 'olympusPen';
    case OLYMPUS_35RC = 'olympus35RC';
}
