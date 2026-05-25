<?php

declare(strict_types=1);

namespace App\Module\Snap\Application\Action\Web;

use App\Common\Domain\HttpStatus;
use App\Infrastructure\Exception\DomainRecordNotFoundException;
use App\Module\Snap\Infrastructure\SnapRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Sqids\Sqids;

class ShowSnapFileAction
{
    public function __construct(
        private readonly SnapRepository $snaps,
        private readonly Sqids $sqids,
    ) {
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        mixed $arguments = [],
    ): ResponseInterface {
        try {
            $filename = (string)$arguments['filename'];
            $parts = explode('.', $filename);

            $decoded = $this->sqids->decode($parts[0]);

            if (empty($decoded)) {
                throw new HttpNotFoundException($request);
            }

            $snap = $this->snaps->ofId($decoded[0]);

            if ($parts[1] !== $snap->getMimeType()->getExt()) {
                throw new HttpNotFoundException($request);
            }

            $response = $response->withHeader('Content-Type', $snap->getMimeType()->value);
            $response = $response->withHeader('Cache-Control', 'public, max-age=31536000');

            $response->getBody()->write($snap->getImage());

            return $response->withStatus(HttpStatus::OK->value);
        } catch (DomainRecordNotFoundException) {
            throw new HttpNotFoundException($request);
        }
    }
}
