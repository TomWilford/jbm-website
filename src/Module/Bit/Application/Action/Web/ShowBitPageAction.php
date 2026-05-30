<?php

declare(strict_types=1);

namespace App\Module\Bit\Application\Action\Web;

use App\Application\Renderer\TwigRenderer;
use App\Infrastructure\Exception\DomainRecordNotFoundException;
use App\Module\Bit\Infrastructure\BitRepository;
use Doctrine\DBAL\Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

final readonly class ShowBitPageAction
{
    public function __construct(private TwigRenderer $renderer, private BitRepository $bits)
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
            return $this->renderer->twig($response, 'bits/show.twig', [
                'bit' => $this->bits->ofId((int)$request->getAttribute('id')),
            ]);
        } catch (DomainRecordNotFoundException) {
            throw new HttpNotFoundException($request);
        }
    }
}
