<?php

declare(strict_types=1);

namespace App\Application\Action;

use App\Application\Renderer\TwigRenderer;
use App\Module\Thing\Infrastructure\ThingRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

final readonly class HomeAction
{
    public function __construct(private TwigRenderer $renderer, private ThingRepository $things)
    {
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     *
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->renderer->twig($response, 'welcome.twig', [
            'things' => $this->things->recent(),
        ]);
    }
}
