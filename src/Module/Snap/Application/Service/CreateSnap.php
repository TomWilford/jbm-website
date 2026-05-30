<?php

declare(strict_types=1);

namespace App\Module\Snap\Application\Service;

use App\Application\Service\CreatorInterface;
use App\Infrastructure\Exception\DomainRecordNotFoundException;
use App\Module\Snap\Application\Validator\CreateSnapValidator;
use App\Module\Snap\Domain\Snap;
use App\Module\Snap\Infrastructure\SnapRepository;
use Doctrine\DBAL\Exception;
use PhpCommonEnums\MimeType\Enumeration\MimeTypeEnum;
use Psr\Http\Message\UploadedFileInterface;
use Respect\Validation\Exceptions\ValidationException;
use Sqids\Sqids;

readonly class CreateSnap implements CreatorInterface
{
    public function __construct(
        private CreateSnapValidator $validator,
        private SnapRepository $repository,
        private Sqids $sqids,
    ) {
    }

    /**
     * @param array{
     *     album_id: string,
     *     image: UploadedFileInterface
     * }|array<string, mixed> $data
     *
     * @throws ValidationException|Exception|DomainRecordNotFoundException
     *
     * @return Snap
     */
    public function createFromArray(array $data): Snap
    {
        $this->validator->validate($data);

        $albumId = $this->sqids->decode($data['album_id']);

        if (empty($albumId)) {
            throw new DomainRecordNotFoundException();
        }

        /** @var UploadedFileInterface $image */
        $image = $data['image'];

        $stream = $image->getStream();
        $binaryString = $stream->getContents();

        $mediaType = (string)$image->getClientMediaType();
        $mimeTypeEnum = MimeTypeEnum::from($mediaType);

        $snap = new Snap(
            id: null,
            albumId: $albumId[0],
            image: $binaryString,
            mimeType: $mimeTypeEnum
        );

        return $this->repository->store($snap);
    }
}
