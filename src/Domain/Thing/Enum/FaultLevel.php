<?php

namespace App\Domain\Thing\Enum;

enum FaultLevel: string
{
    case ALL = 'all';
    case MOSTLY = 'most';
    case PARTLY = 'part';
}
