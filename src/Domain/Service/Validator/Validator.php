<?php

declare(strict_types=1);

namespace App\Domain\Service\Validator;

abstract readonly class Validator implements ValidatorInterface
{
    protected \Respect\Validation\Validator $v;

    public function __construct()
    {
        $this->v = new \Respect\Validation\Validator();
    }
}
