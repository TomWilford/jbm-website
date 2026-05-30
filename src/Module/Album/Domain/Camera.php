<?php

declare(strict_types=1);

namespace App\Module\Album\Domain;

enum Camera: string
{
    case YASHICA_635 = 'yashica635';
    case OLYMPUS_PEN = 'olympusPen';
    case OLYMPUS_35RC = 'olympus35RC';
    case CHAIKA_2 = 'chaika2';
    case PENTAX_LX = 'pentaxLX';
    case PENTAX_ME_SUPER = 'pentaxMESuper';
    case ZENIT_E = 'zenitE';
    case RICOH_KR10_SUPER = 'ricohKr10Super';

    public function getDisplayName(): string
    {
        return match ($this) {
            self::OLYMPUS_35RC => 'Olympus 35RC',
            self::PENTAX_LX => 'Pentax LX',
            self::PENTAX_ME_SUPER => 'Pentax ME Super',
            self::RICOH_KR10_SUPER => 'Ricoh KR10 Super',
            default => ucwords(strtolower(str_replace('_', ' ', $this->name))),
        };
    }
}
