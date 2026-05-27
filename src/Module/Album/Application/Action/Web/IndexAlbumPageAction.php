<?php

declare(strict_types=1);

namespace App\Module\Album\Application\Action\Web;

use App\Application\Renderer\TwigRenderer;
use App\Module\Album\Infrastructure\AlbumRepository;
use Doctrine\DBAL\Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

final readonly class IndexAlbumPageAction
{
    public function __construct(private TwigRenderer $renderer, private AlbumRepository $albums)
    {
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     *
     * @throws Exception
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->renderer->twig($response, 'albums/index.twig', [
            'albums' => $this->albums->all(),
        ]);
    }
}
