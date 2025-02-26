<?php

declare(strict_types=1);

namespace App\Action\Bits\Page;

use App\Domain\Bit\Repository\BitRepository;
use App\Renderer\TwigRenderer;
use Doctrine\DBAL\Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

final readonly class IndexAction
{
    public function __construct(private TwigRenderer $renderer, private BitRepository $bits)
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
        return $this->renderer->twig($response, 'bits/index.twig', [
            'bits' => $this->bits->all(),
        ]);
    }
}
