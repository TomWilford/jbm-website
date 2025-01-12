<?php

namespace App\Infrastructure\Service\Validator;

use App\Infrastructure\Service\Validator\ValidatorInterface;

abstract readonly class Validator implements ValidatorInterface
{
    protected \Respect\Validation\Validator $v;

    public function __construct()
    {
        $this->v = new \Respect\Validation\Validator();
    }
}
