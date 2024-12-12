<?php

namespace App\Domain\Enums;

enum FaultLevel: string
{
    case ALL = 'all';
    case MOSTLY = 'most';
    case PARTIALLY = 'part';
}
