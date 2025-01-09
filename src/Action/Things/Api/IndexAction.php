<?php

namespace App\Action\Things\Api;

use App\Domain\Thing\ThingRepository;
use App\Infrastructure\Enum\HttpStatus;
use App\Renderer\JsonRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final readonly class IndexAction
{
    public function __construct(private JsonRenderer $renderer, private ThingRepository $things)
    {
        //
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $status = HttpStatus::OK;
        try {
            $things = $this->things->all();
            if (empty($things)) {
                $status = HttpStatus::NO_CONTENT;
            }
        } catch (\Throwable $exception) {
            $status = HttpStatus::INTERNAL_SERVER_ERROR;
            $things = [];
        }

        return $this->renderer->jsonWithStatus(
            $response,
            $things,
            $status
        );
    }
}
