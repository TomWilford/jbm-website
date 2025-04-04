<?php

declare(strict_types=1);

namespace App\Module\Thing\Index;

use App\Application\Renderer\TwigRenderer;
use App\Module\Thing\Infrastructure\ThingRepository;
use Doctrine\DBAL\Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

final readonly class PageIndexAction
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
     * @throws LoaderError|Exception
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->renderer->twig($response, 'things/index.twig', [
            'things' => $this->things->all(),
        ]);
    }
}
