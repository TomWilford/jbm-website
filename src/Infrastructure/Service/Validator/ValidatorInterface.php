<?php

namespace App\Infrastructure\Service\Validator;

interface ValidatorInterface
{
    public function validate(array $data): void;
}
