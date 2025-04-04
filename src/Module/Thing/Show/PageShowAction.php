<?php

declare(strict_types=1);

namespace App\Module\Thing\Show;

use App\Application\Renderer\TwigRenderer;
use App\Domain\Exception\DomainRecordNotFoundException;
use App\Module\Thing\Infrastructure\ThingRepository;
use Doctrine\DBAL\Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

final readonly class PageShowAction
{
    public function __construct(private TwigRenderer $renderer, private ThingRepository $things)
    {
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param mixed $arguments
     *
     * @return ResponseInterface
     * @throws Exception
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        mixed $arguments = [],
    ): ResponseInterface {
        try {
            return $this->renderer->twig($response, 'things/show.twig', [
                'thing' => $this->things->ofId((int)$arguments['sqid']),
            ]);
        } catch (DomainRecordNotFoundException) {
            throw new HttpNotFoundException($request);
        }
    }
}
