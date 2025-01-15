<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\Validator;

interface ValidatorInterface
{
    public function validate(array $data): void;
}
