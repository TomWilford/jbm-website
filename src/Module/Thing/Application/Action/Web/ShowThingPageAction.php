<?php

declare(strict_types=1);

namespace App\Module\Thing\Application\Action\Web;

use App\Application\Renderer\TwigRenderer;
use App\Infrastructure\Exception\DomainRecordNotFoundException;
use App\Module\Thing\Infrastructure\ThingRepository;
use Doctrine\DBAL\Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

final readonly class ShowThingPageAction
{
    public function __construct(private TwigRenderer $renderer, private ThingRepository $things)
    {
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
            return $this->renderer->twig($response, 'things/show.twig', [
                'thing' => $this->things->ofId((int)$request->getAttribute('id')),
            ]);
        } catch (DomainRecordNotFoundException) {
            throw new HttpNotFoundException($request);
        }
    }
}
