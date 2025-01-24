<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use App\Database\Seeds\SeedInterface;
use App\Domain\Bit\Bit;
use App\Domain\Bit\Enum\Language;
use App\Domain\Bit\Repository\BitRepository;
use App\Infrastructure\Persistence\RepositoryInterface;

class BitsSeed implements SeedInterface
{
    public function __construct(private BitRepository $repository)
    {
        //
    }

    public function getName(): string
    {
        return 'Bits';
    }

    public function getRepository(): BitRepository
    {
        return $this->repository;
    }

    /**
     * @return Bit[]
     */
    public function getData(): array
    {
        return [
            new Bit(
                id: null,
                name: 'Backed Enum Value Array',
                code: <<<'PHP'
                <?php

                enum Whatever: string
                {
                    case YEH = 'yes';
                    case NAH = 'no';
                    case MEH = 'maybe';
                
                    public static function values(): array
                    {
                        return array_map(fn(self $case) => $case->value, self::cases());
                    }
                }
                
                var_dump(Whatever::values());

                PHP,
                language: Language::PHP,
                returns: <<<'PHP'
                array(3) {
                   [0]=>
                   string(3) "yes"
                   [1]=>
                   string(2) "no"
                   [2]=>
                   string(5) "maybe"
                 }
                PHP
            ),
            new Bit(
                id: null,
                name: 'Enum Name Array',
                code: <<<'PHP'
                <?php

                enum Stuff
                {
                    case TOGGLES;
                    case WIDGETS;
                
                    public static function values(): array
                    {
                        return array_map(fn(self $item): string => $item->name, self::cases());
                    }
                }
                
                var_dump(Stuff::values());

                PHP,
                language: Language::PHP,
                returns: <<<'PHP'
                array(2) {
                  [0]=>
                  string(7) "TOGGLES"
                  [1]=>
                  string(7) "WIDGETS"
                }
                PHP
            )
        ];
    }
}
