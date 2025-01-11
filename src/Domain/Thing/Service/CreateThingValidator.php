<?php

declare(strict_types=1);

namespace App\Domain\Thing\Service;

use App\Domain\Thing\Enum\FaultLevel;
use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validator;

readonly class CreateThingValidator
{
    public function __construct(private Validator $v)
    {
        //
    }

    /**
     * @param array{
     *     name: string,
     *     shortDescription: string,
     *     description: string,
     *     featured: bool,
     *     url: string,
     *     faultLevel: string,
     *     activeFrom: string,
     *     activeTo: string
     *     } $data
     * @throws ValidationException
     */
    public function validateCreateThing(array $data): void
    {
        $this->v::key('name', $this->v::stringType()->length(1, 21))
            ->key('shortDescription', $this->v::stringType()->length(1, 45))
            ->key('description', $this->v::stringType()->length(1, 255))
            ->key('featured', $this->v::boolType()->notEmpty())
            ->key('url', $this->v::optional($this->v::url()))
            ->key('faultLevel', $this->v::in(FaultLevel::values()))
            ->key('activeFrom', $this->v::date())
            ->key('activeTo', $this->v::optional($this->v::date()))
            ->assert($data);
    }
}
