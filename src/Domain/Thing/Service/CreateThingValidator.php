<?php

declare(strict_types=1);

namespace App\Domain\Thing\Service;

use App\Domain\Thing\Enum\FaultLevel;
use Respect\Validation\Exceptions\ValidationException;
use App\Infrastructure\Service\Validator\Validator;

readonly class CreateThingValidator extends Validator
{
    /**
     * @param array{
     *     name: string,
     *     short_description: string,
     *     description: string,
     *     featured: bool,
     *     url: string,
     *     fault_level: string,
     *     active_from: string,
     *     active_to: string
     * } $data
     * @throws ValidationException
     */
    public function validate(array $data): void
    {
        $this->v::key('name', $this->v::stringType()->length(1, 21))
            ->key('short_description', $this->v::stringType()->length(1, 45))
            ->key('description', $this->v::stringType()->length(1, 255))
            ->key('featured', $this->v::boolVal()->notEmpty())
            ->key('url', $this->v::optional($this->v::url()))
            ->key('fault_level', $this->v::in(FaultLevel::values()))
            ->key('active_from', $this->v::date())
            ->key('active_to', $this->v::optional($this->v::date()))
            ->assert($data);
    }
}
