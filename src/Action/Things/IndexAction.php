<?php

declare(strict_types=1);

namespace App\Action\Things;

use App\Domain\Thing\ThingRepository;
use App\Renderer\TwigRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

final readonly class IndexAction
{
    public function __construct(private TwigRenderer $renderer, private ThingRepository $things)
    {
        //
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->renderer->twig($response, 'things/index.twig', [
            'things' => $this->things->all(),
        ]);
    }
}