<?php

declare(strict_types=1);

namespace App\Module\Snap\Application\Service;

use App\Application\Service\CreatorInterface;
use App\Module\Snap\Application\Validator\CreateSnapValidator;
use App\Module\Snap\Domain\Snap;
use App\Module\Snap\Infrastructure\SnapRepository;
use Doctrine\DBAL\Exception;
use PhpCommonEnums\MimeType\Enumeration\MimeTypeEnum;
use Psr\Http\Message\UploadedFileInterface;
use Respect\Validation\Exceptions\ValidationException;

readonly class CreateSnap implements CreatorInterface
{
    public function __construct(
        private CreateSnapValidator $validator,
        private SnapRepository $repository,
    ) {
    }

    /**
     * @param array{
     *     album_id: int|string,
     *     image: UploadedFileInterface
     * }|array<string, mixed> $data
     *
     * @throws ValidationException|Exception
     *
     * @return Snap
     */
    public function createFromArray(array $data): Snap
    {
        $this->validator->validate($data);

        /** @var UploadedFileInterface $image */
        $image = $data['image'];

        $stream = $image->getStream();
        $binaryString = $stream->getContents();

        $mediaType = (string) $image->getClientMediaType();
        $mimeTypeEnum = MimeTypeEnum::from($mediaType);

        $snap = new Snap(
            id: null,
            albumId: (int)$data['album_id'],
            image: $binaryString,
            mimeType: $mimeTypeEnum
        );

        return $this->repository->store($snap);
    }
}
