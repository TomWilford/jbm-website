<?php

declare(strict_types=1);

namespace App\Module\Snap\Application\Action\Api;

use App\Application\Renderer\JsonRenderer;
use App\Common\Domain\HttpStatus;
use App\Module\Snap\Application\Service\CreateSnap;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Exceptions\ValidationException;
use Throwable;

class CreateSnapAction
{
    public function __construct(private readonly JsonRenderer $renderer, private readonly CreateSnap $creator)
    {
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        try {
            $status = HttpStatus::OK;
            $input = (array)$request->getParsedBody();

            /** @var array<UploadedFileInterface> $uploadedFiles */
            $uploadedFiles = $request->getUploadedFiles();

            if (!isset($uploadedFiles['image'])) {
                throw new InvalidArgumentException('File upload is required');
            }

            $uploadedFile = $uploadedFiles['image'];

            if ($uploadedFile->getError() !== UPLOAD_ERR_OK) {
                throw new InvalidArgumentException('File upload failed');
            }

            $input['image'] = $uploadedFile;

            $data = $this->creator->createFromArray($input);
        } catch (ValidationException $exception) {
            $status = HttpStatus::BAD_REQUEST;
            /** @var NestedValidationException $exception */
            $data = [$exception->getMessages()];
        } catch (Throwable $exception) {
            $status = HttpStatus::INTERNAL_SERVER_ERROR;
            $data = ['An unknown error occurred. Sorry about that.'];
            error_log($exception->getMessage());
        }

        return $this->renderer->jsonWithStatus($response, $data, $status);
    }
}
