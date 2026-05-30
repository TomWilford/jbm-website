<?php

declare(strict_types=1);

namespace App\Module\Snap\Application\Service;

use App\Module\Snap\Domain\Orientation;

class ImageService
{
    public function getOrientation(string $binary): Orientation
    {
        $info = @getimagesizefromstring($binary);

        if (!$info || $info[0] === 0 || $info[1] === 0) {
            return Orientation::SQUARE;
        }

        $ratio = $info[0] / $info[1];

        return match (true) {
            $ratio > 1.1 => Orientation::LANDSCAPE,
            $ratio < 0.9 => Orientation::PORTRAIT,
            default => Orientation::SQUARE,
        };
    }
}
