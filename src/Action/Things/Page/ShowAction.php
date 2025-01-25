<?php

declare(strict_types=1);

namespace App\Action\Things\Page;

use App\Domain\Exception\DomainRecordNotFoundException;
use App\Domain\Thing\Repository\ThingRepository;
use App\Renderer\TwigRenderer;
use Doctrine\DBAL\Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

final readonly class ShowAction
{
    public function __construct(private TwigRenderer $renderer, private ThingRepository $things)
    {
        //
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError|Exception
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        mixed $arguments = []
    ): ResponseInterface {

        try {
            return $this->renderer->twig($response, 'things/show.twig', [
                'thing' => $this->things->ofId((int)$arguments['id']),
            ]);
        } catch (DomainRecordNotFoundException) {
            throw new HttpNotFoundException($request);
        }
    }
}
