<?php

namespace App\Action\Things\Api;

use App\Domain\Thing\ThingRepository;
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
        $status = 'success';
        $statusCode = 200;
        if (empty($this->things)) {
            $status = 'error';
            $statusCode = 400;
        }

        return $this->renderer->jsonResponse(
            $response,
            [
                'status' => $status,
                'data' => $this->things->all()
            ],
            $statusCode
        );
    }
}
