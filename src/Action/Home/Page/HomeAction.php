<?php

declare(strict_types=1);

namespace App\Action\Home\Page;

use App\Domain\Thing\Repository\ThingRepository;
use App\Renderer\TwigRenderer;
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
