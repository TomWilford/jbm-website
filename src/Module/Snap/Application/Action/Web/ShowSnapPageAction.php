<?php

declare(strict_types=1);

namespace App\Module\Snap\Application\Action\Web;

use App\Application\Renderer\TwigRenderer;
use App\Infrastructure\Exception\DomainRecordNotFoundException;
use App\Module\Album\Infrastructure\AlbumRepository;
use App\Module\Snap\Infrastructure\SnapRepository;
use Doctrine\DBAL\Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

final class ShowSnapPageAction
{
    public function __construct(
        private readonly TwigRenderer $renderer,
        private readonly SnapRepository $snaps,
        private readonly AlbumRepository $albums,
    ) {
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array<string> $arguments
     *
     * @throws Exception
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     *
     * @return ResponseInterface
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $arguments = [],
    ): ResponseInterface {
        try {
            $snap = $this->snaps->ofId((int)$request->getAttribute('id'));
            $album = $this->albums->ofId($snap->getAlbumId());

            return $this->renderer->twig($response, 'snaps/show.twig', [
                'snap' => $snap,
                'album' => $album,
            ]);
        } catch (DomainRecordNotFoundException) {
            throw new HttpNotFoundException($request);
        }
    }
}
