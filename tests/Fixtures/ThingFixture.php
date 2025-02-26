<?php

declare(strict_types=1);

namespace App\Test\Fixtures;

class ThingFixture implements FixtureInterface
{
    private string $table = 'things';

    /**
     * @var array{array{
     *      id: int,
     *      name: string,
     *      short_description: string,
     *      description: string,
     *      featured: bool,
     *      fault_level: string,
     *      active_from: int,
     *      active_to: ?int,
     *      url: ?string,
     *      created_at: int,
     *      updated_at: int
     *  }} $records
     */
    private array $records = [
        [
            'id' => 1,
            'name' => 'Thing 1',
            'short_description' => 'Short description',
            'description' => 'Long description',
            'featured' => true,
            'url' => 'https://test.test',
            'fault_level' => 'all',
            'active_from' => 1471298400,
            'active_to' => null,
            'created_at' => 1471298400,
            'updated_at' => 1471298400,
        ],
        [
            'id' => 99,
            'name' => 'Thing 99',
            'short_description' => 'Short description 99',
            'description' => 'Long description 00',
            'featured' => false,
            'url' => 'https://test.test',
            'fault_level' => 'all',
            'active_from' => 1471298400,
            'active_to' => null,
            'created_at' => 1471298400,
            'updated_at' => 1471298400,
        ],
    ];

    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @return array{array{string:mixed}}
     */
    public function getRecords(): array
    {
        return $this->records;
    }
}
