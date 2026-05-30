<?php

declare(strict_types=1);

namespace App\Module\Bit\Application\Action\Api;

use App\Application\Renderer\JsonRenderer;
use App\Common\Domain\HttpStatus;
use App\Module\Bit\Application\Service\CreateBit;
use App\Module\Bit\Application\Validator\CreateBitValidator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Exceptions\ValidationException;
use Throwable;

final readonly class CreateBitAction
{
    public function __construct(
        private JsonRenderer $renderer,
        private CreateBitValidator $validator,
        private CreateBit $creator,
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
