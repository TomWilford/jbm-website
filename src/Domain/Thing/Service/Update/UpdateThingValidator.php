<?php

declare(strict_types=1);

namespace App\Domain\Thing\Service\Update;

use App\Domain\Thing\Enum\FaultLevel;
use App\Infrastructure\Service\Validator\Validator;
use Respect\Validation\Exceptions\ValidationException;

readonly class UpdateThingValidator extends Validator
{
    /**
     * @param array{
     *     name: string,
     *     short_description: string,
     *     description: string,
     *     featured: string,
     *     fault_level: string,
     *     active_from: string,
     *     active_to: string,
     *     url: string,
     * }|array<string, mixed> $data
     * @throws ValidationException
     */
    public function validate(array $data): void
    {
        $this->v::key('name', $this->v::optional($this->v::stringType()->length(1, 21)))
            ->key('short_description', $this->v::optional($this->v::stringType()->length(1, 45)))
            ->key('description', $this->v::optional($this->v::stringType()->length(1, 255)))
            ->key('featured', $this->v::optional($this->v::boolVal()))
            ->key('fault_level', $this->v::optional($this->v::in(FaultLevel::values())))
            ->key('active_from', $this->v::optional($this->v::date()))
            ->key('active_to', $this->v::optional($this->v::anyOf($this->v::date(), $this->v::equals('null'))))
            ->key('url', $this->v::optional($this->v::anyOf($this->v::url(), $this->v::equals('null'))))
            ->assert($data);
    }
}
