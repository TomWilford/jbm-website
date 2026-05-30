<?php

declare(strict_types=1);

namespace App\Module\Album\Application\Action\Web;

use App\Application\Renderer\TwigRenderer;
use App\Infrastructure\Exception\DomainRecordNotFoundException;
use App\Module\Album\Infrastructure\AlbumRepository;
use App\Module\Snap\Application\Service\SnapLayout;
use App\Module\Snap\Infrastructure\SnapRepository;
use Doctrine\DBAL\Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

final class ShowAlbumPageAction
{
    public function __construct(
        private readonly TwigRenderer $renderer,
        private readonly AlbumRepository $albums,
        private readonly SnapRepository $snaps,
        private readonly SnapLayout $snapLayout,
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
            $album = $this->albums->ofId((int)$request->getAttribute('id'));
            $snaps = $this->snaps->ofAlbumId((int)$album->getId());
            $rows = $this->snapLayout->buildRows((array)$snaps);

            return $this->renderer->twig($response, 'albums/show.twig', [
                'album' => $album,
                'snaps' => $snaps,
                'rows' => $rows,
            ]);
        } catch (DomainRecordNotFoundException) {
            throw new HttpNotFoundException($request);
        }
    }
}
