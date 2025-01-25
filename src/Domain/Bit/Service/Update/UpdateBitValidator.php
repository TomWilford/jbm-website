<?php

declare(strict_types=1);

namespace App\Domain\Bit\Service\Update;

use App\Domain\Bit\Enum\Language;
use App\Infrastructure\Service\Validator\Validator;
use Respect\Validation\Exceptions\ValidationException;

readonly class UpdateBitValidator extends Validator
{
    /**
     * @param array{
     *     name: string,
     *     code: string,
     *     description: string,
     *     returns: string
     *  }|array<string, mixed> $data
     * @throws ValidationException
     */
    public function validate(array $data): void
    {
        $this->v::key('name', $this->v::optional($this->v::stringType()->length(1, 66)))
            ->key('code', $this->v::optional($this->v::stringType()->length(1, 255)))
            ->key('language', $this->v::optional($this->v::in(Language::values())))
            ->key('description', $this->v::optional($this->v::stringType()->length(1, 255)))
            ->key('returns', $this->v::optional($this->v::stringType()->length(1, 255)))
            ->assert($data);
    }
}
