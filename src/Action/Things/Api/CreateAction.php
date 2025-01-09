<?php

declare(strict_types=1);

namespace App\Action\Things\Api;

use App\Domain\Thing\ThingRepository;
use App\Renderer\JsonRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final readonly class CreateAction
{
    public function __construct(private JsonRenderer $renderer, private ThingRepository $things)
    {
        //
    }

    /**
     * @param array{id: string} $arguments
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $arguments
    ): ResponseInterface {
        return $this->renderer->jsonWithStatus($response);
    }
}