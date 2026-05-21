<?php

declare(strict_types=1);

namespace App\Module\Album\Application\Validator;

use App\Application\Validator\Validator;
use App\Module\Album\Domain\Camera;
use Respect\Validation\Exceptions\ValidationException;

readonly class CreateAlbumValidator extends Validator
{
    /**
     * @param array{
     *     name: string,
     *     camera: string,
     *     location: string,
     *     date: string
     * }|array<string, mixed> $data
     *
     * @throws ValidationException
     */
    public function validate(array $data): void
    {
        $this->v::key('name', $this->v::stringType()->length(1, 255))
            ->key('camera', $this->v::in(array_column(Camera::cases(), 'value')))
            ->key('location', $this->v::stringType()->length(1, 255))
            ->key('date', $this->v::date())
            ->assert($data);
    }
}
