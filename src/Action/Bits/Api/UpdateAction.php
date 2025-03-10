<?php

declare(strict_types=1);

namespace App\Action\Bits\Api;

use App\Domain\Bit\Repository\BitRepository;
use App\Domain\Bit\Service\Update\BitUpdater;
use App\Domain\Bit\Service\Update\UpdateBitValidator;
use App\Domain\Exception\DomainRecordNotFoundException;
use App\Infrastructure\Enum\HttpStatus;
use App\Renderer\JsonRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Exceptions\ValidationException;
use Throwable;

final readonly class UpdateAction
{
    public function __construct(
        private JsonRenderer $renderer,
        private BitRepository $bits,
        private UpdateBitValidator $validator,
        private BitUpdater $updater,
    ) {
    }

    /**
     * @param array{id: string} $arguments
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $arguments,
    ): ResponseInterface {
        try {
            $status = HttpStatus::OK;
            $thing = $this->bits->ofId((int)$arguments['id']);
            $this->validator->validate((array)$request->getParsedBody());
            $data = $this->updater->updateFromArray((array)$request->getParsedBody(), $thing);
        } catch (DomainRecordNotFoundException $exception) {
            $status = HttpStatus::NOT_FOUND;
            $data = [$exception->getMessage()];
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
