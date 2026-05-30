<?php

declare(strict_types=1);

namespace App\Test\Fixtures;

class AlbumFixture extends BaseFixture
{
    protected string $table = 'albums';
    protected array $records = [
        [
            'id' => 1,
            'name' => 'Album 1',
            'camera' => 'yashica635',
            'location' => 'Tokyo',
            'date' => '2019-01-01',
            'created_at' => 1471298400,
            'updated_at' => 1471298400,
        ],
        [
            'id' => 99,
            'name' => 'Album 99',
            'camera' => 'olympus35Rc',
            'location' => 'Leeds',
            'date' => '2023-01-01',
            'created_at' => 1471298400,
            'updated_at' => 1471298400,
        ],
    ];
}
