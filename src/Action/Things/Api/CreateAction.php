<?php

declare(strict_types=1);

namespace App\Action\Things\Api;

use App\Domain\Thing\Repository\ThingRepository;
use App\Domain\Thing\Service\CreateThingValidator;
use App\Renderer\JsonRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Exceptions\ValidationException;

final readonly class CreateAction
{
    public function __construct(
        private JsonRenderer $renderer,
        private ThingRepository $things,
        private CreateThingValidator $validator
    ) {
        //
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $arguments
    ): ResponseInterface {
        /*try {
            $this->validator->validateCreateThing();
        } catch (ValidationException $exception) {

        } catch (\Throwable $exception) {

        }*/

        return $this->renderer->jsonWithStatus($response);
    }
}
