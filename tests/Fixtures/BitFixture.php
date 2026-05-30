<?php

declare(strict_types=1);

namespace App\Test\Fixtures;

class BitFixture extends BaseFixture
{
    protected string $table = 'bits';
    /**
     * @var array{array{
     *     id: int,
     *     name: string,
     *     code: string,
     *     description: ?string,
     *     returns: ?string,
     *     created_at: int,
     *     updated_at: int
     * }} $records
     */
    protected array $records = [
        [
            'id' => 1,
            'name' => 'Test Bit',
            'code' => "var_dump(sprintf('%s %s!', 'Hello', 'World'));",
            'language' => 'PHP',
            'description' => 'Test bit description',
            'returns' => 'string(12) "Hello World!"',
            'created_at' => 1600000000,
            'updated_at' => 1601000000,
        ],
        [
            'id' => 99,
            'name' => 'Test Bit 99',
            'code' => "var_dump(sprintf('%s %s!', 'Hello', 'World'));",
            'language' => 'PHP',
            'description' => 'Test bit 99 description',
            'returns' => 'string(12) "Hello World!"',
            'created_at' => 1600000000,
            'updated_at' => 1601000000,
        ],
    ];
}
