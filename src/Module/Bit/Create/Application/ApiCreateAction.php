<?php

declare(strict_types=1);

namespace App\Module\Bit\Create\Application;

use App\Application\Renderer\JsonRenderer;
use App\Common\Enum\HttpStatus;
use App\Module\Bit\Create\Domain\BitCreator;
use App\Module\Bit\Create\Domain\CreateBitValidator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Exceptions\ValidationException;
use Throwable;

final readonly class ApiCreateAction
{
    public function __construct(
        private JsonRenderer $renderer,
        private CreateBitValidator $validator,
        private BitCreator $creator,
    ) {
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        try {
            $status = HttpStatus::OK;
            $input = (array)$request->getParsedBody();
            $this->validator->validate($input);
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
