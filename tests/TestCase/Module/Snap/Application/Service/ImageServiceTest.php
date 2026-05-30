<?php

declare(strict_types=1);

namespace App\Test\TestCase\Module\Snap\Application\Service;

use App\Module\Snap\Application\Service\ImageService;
use App\Module\Snap\Domain\Orientation;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ImageService::class)]
class ImageServiceTest extends TestCase
{
    public function testGetOrientationReturnsSquareForSquareBinary(): void
    {
        $square = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
        $sut = new ImageService();

        $this->assertSame(Orientation::SQUARE, $sut->getOrientation($square));
    }

    public function testGetOrientationReturnsLandscapeForLandscapeBinary(): void
    {
        $square = base64_decode('R0lGODlhAgABAIAAAAAAAP///yH5BAEAAAAALAAAAAACAAEAAAICRAA7');
        $sut = new ImageService();

        $this->assertSame(Orientation::LANDSCAPE, $sut->getOrientation($square));
    }

    public function testGetOrientationReturnsPortraitForPortraitBinary(): void
    {
        $square = base64_decode('R0lGODlhAQACAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAIAAAICRAA7');
        $sut = new ImageService();

        $this->assertSame(Orientation::PORTRAIT, $sut->getOrientation($square));
    }

    public function testGetOrientationReturnsSquareForInvalidBinary(): void
    {
        $square = base64_decode('beep_boop');
        $sut = new ImageService();

        $this->assertSame(Orientation::SQUARE, $sut->getOrientation($square));
    }
}
