<?php

declare(strict_types=1);

namespace App\Action\Things\Api;

use App\Domain\Exception\DomainRecordNotFoundException;
use App\Domain\Thing\Repository\ThingRepository;
use App\Domain\Thing\Service\Update\ThingUpdater;
use App\Domain\Thing\Service\Update\UpdateThingValidator;
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
        private ThingRepository $things,
        private UpdateThingValidator $validator,
        private ThingUpdater $updater,
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
            $thing = $this->things->ofId((int)$arguments['sqid']);
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
