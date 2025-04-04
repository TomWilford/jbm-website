<?php

declare(strict_types=1);

namespace App\Module\Bit\Update\Application;

use App\Application\Renderer\JsonRenderer;
use App\Common\Enum\HttpStatus;
use App\Domain\Exception\DomainRecordNotFoundException;
use App\Module\Bit\Infrastructure\BitRepository;
use App\Module\Bit\Update\Domain\BitUpdater;
use App\Module\Bit\Update\Domain\UpdateBitValidator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Exceptions\ValidationException;
use Throwable;

final readonly class ApiUpdateAction
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
            $thing = $this->bits->ofId((int)$arguments['sqid']);
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
