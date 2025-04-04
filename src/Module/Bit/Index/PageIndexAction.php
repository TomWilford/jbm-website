<?php

declare(strict_types=1);

namespace App\Module\Bit\Index;

use App\Application\Renderer\TwigRenderer;
use App\Module\Bit\Infrastructure\BitRepository;
use Doctrine\DBAL\Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

final readonly class PageIndexAction
{
    public function __construct(private TwigRenderer $renderer, private BitRepository $bits)
    {
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     * @throws Exception
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->renderer->twig($response, 'bits/index.twig', [
            'bits' => $this->bits->all(),
        ]);
    }
}
