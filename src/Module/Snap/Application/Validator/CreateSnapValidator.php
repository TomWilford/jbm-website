<?php

declare(strict_types=1);

namespace App\Module\Snap\Application\Validator;

use App\Application\Validator\Validator;
use PhpCommonEnums\MimeType\Enumeration\MimeTypeEnum;
use Psr\Http\Message\UploadedFileInterface;
use Respect\Validation\Exceptions\ValidationException;

readonly class CreateSnapValidator extends Validator
{
    /**
     * @param array{
     *     album_id: int|string,
     *     file: UploadedFileInterface
     * }|array<string, mixed> $data
     *
     * @throws ValidationException
     */
    public function validate(array $data): void
    {
        $this->v::key('album_id', $this->v::intVal()->notEmpty())
            ->key('image', $this->v::uploaded()
                ->size('0KB', '10MB')
                ->callback(function (UploadedFileInterface $image): bool {
                    $mediaType = $image->getClientMediaType();

                    if ($mediaType === null) {
                        return false;
                    }

                    return in_array($mediaType, $this->getImageMimeTypes(), true);
                }))
            ->assert($data);
    }

    /**
     * @return array<string>
     */
    private function getImageMimeTypes(): array
    {
        return [
            MimeTypeEnum::ImageWebp->value,
            MimeTypeEnum::ImagePng->value,
            MimeTypeEnum::ImageGif->value,
            MimeTypeEnum::ImageJpeg->value,
        ];
    }
}
