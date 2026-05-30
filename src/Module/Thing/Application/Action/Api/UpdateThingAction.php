<?php

declare(strict_types=1);

namespace App\Module\Thing\Application\Action\Api;

use App\Application\Renderer\JsonRenderer;
use App\Common\Domain\HttpStatus;
use App\Infrastructure\Exception\DomainRecordNotFoundException;
use App\Module\Thing\Application\Service\UpdateThing;
use App\Module\Thing\Application\Validator\UpdateThingValidator;
use App\Module\Thing\Infrastructure\ThingRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Exceptions\ValidationException;
use Throwable;

final readonly class UpdateThingAction
{
    public function __construct(
        private JsonRenderer $renderer,
        private ThingRepository $things,
        private UpdateThingValidator $validator,
        private UpdateThing $updater,
    ) {
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array<string> $arguments
     *
     * @return ResponseInterface
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $arguments,
    ): ResponseInterface {
        try {
            $status = HttpStatus::OK;
            $thing = $this->things->ofId((int)$request->getAttribute('id'));
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
