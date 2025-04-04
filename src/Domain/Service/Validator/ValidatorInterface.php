<?php

declare(strict_types=1);

namespace App\Domain\Service\Validator;

use Respect\Validation\Exceptions\ValidationException;

interface ValidatorInterface
{
    /**
     * @param array<string, mixed> $data
     *
     * @throws ValidationException
     */
    public function validate(array $data): void;
}
