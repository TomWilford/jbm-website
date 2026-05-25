<?php

declare(strict_types=1);

namespace App\Module\Album\Application\Service;

use App\Application\Service\CreatorInterface;
use App\Module\Album\Application\Validator\CreateAlbumValidator;
use App\Module\Album\Domain\Album;
use App\Module\Album\Domain\Camera;
use App\Module\Album\Infrastructure\AlbumRepository;
use Respect\Validation\Exceptions\ValidationException;

readonly class CreateAlbum implements CreatorInterface
{
    public function __construct(
        private CreateAlbumValidator $validator,
        private AlbumRepository $repository,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     *
     * @throws ValidationException
     */
    public function createFromArray(array $data): Album
    {
        $this->validator->validate($data);

        $album = new Album(
            id: null,
            name: $data['name'],
            camera: Camera::from($data['camera']),
            location: $data['location'],
            date: $data['date']
        );

        return $this->repository->store($album);
    }
}
