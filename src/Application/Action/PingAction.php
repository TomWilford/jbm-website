<?php

declare(strict_types=1);

namespace App\Application\Action;

use App\Application\Renderer\JsonRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class PingAction
{
    private JsonRenderer $renderer;

    public function __construct(JsonRenderer $renderer)
    {
        $this->renderer = $renderer;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->renderer->json($response, ['success' => true]);
    }
}
