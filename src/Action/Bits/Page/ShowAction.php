<?php

declare(strict_types=1);

namespace App\Action\Bits\Page;

use App\Domain\Bit\Repository\BitRepository;
use App\Domain\Exception\DomainRecordNotFoundException;
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
    public function __construct(private TwigRenderer $renderer, private BitRepository $bits)
    {
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param mixed $arguments
     *
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError|Exception
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        mixed $arguments = [],
    ): ResponseInterface {
        try {
            return $this->renderer->twig($response, 'bits/show.twig', [
                'bit' => $this->bits->ofId((int)$arguments['sqid']),
            ]);
        } catch (DomainRecordNotFoundException) {
            throw new HttpNotFoundException($request);
        }
    }
}
