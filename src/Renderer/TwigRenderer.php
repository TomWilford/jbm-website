<?php

declare(strict_types=1);

namespace App\Renderer;

use Psr\Http\Message\ResponseInterface;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

readonly class TwigRenderer
{
    public function __construct(private Twig $twig)
    {
    }

    /**
     * @param ResponseInterface $response
     * @param string $template
     * @param mixed|null $data
     *
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function twig(
        ResponseInterface $response,
        string $template,
        mixed $data = null,
    ): ResponseInterface {
        return $this->twig->render($response, $template, $data);
    }
}
