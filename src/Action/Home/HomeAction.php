<?php

namespace App\Action\Home;

use App\Renderer\TwigRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

final readonly class HomeAction
{
    public function __construct(private TwigRenderer $renderer)
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
        return $this->renderer->twig($response, 'welcome.twig', [
            'world' => 'werld'
        ]);
    }
}
