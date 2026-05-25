<?php

declare(strict_types=1);

namespace App\Test\Fixtures;

use Nyholm\Psr7\Stream;
use Psr\Http\Message\StreamInterface;

class SnapFixture extends BaseFixture
{
    protected string $table = 'snaps';
    protected array $records = [];
    private ?StreamInterface $exampleImage = null;

    public function __construct()
    {
        $this->records = [
            [
                'id' => 1,
                'album_id' => 1,
                'image' => $this->getExampleImage()->getContents(),
                'mime_type' => 'image/webp',
                'created_at' => 1471298400,
                'updated_at' => 1471298400,
            ],
            [
                'id' => 99,
                'album_id' => 1,
                'image' => $this->getExampleImage()->getContents(),
                'mime_type' => 'image/webp',
                'created_at' => 1471298400,
                'updated_at' => 1471298400,
            ],
        ];
    }

    private function getExampleImage(): StreamInterface
    {
        if ($this->exampleImage === null) {
            $this->exampleImage = new Stream(
                fopen(dirname(__DIR__) . '/Fixtures/assets/snap-01.webp', 'r')
            );
        }

        return $this->exampleImage;
    }
}
