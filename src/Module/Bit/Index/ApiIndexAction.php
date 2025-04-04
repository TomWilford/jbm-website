<?php

declare(strict_types=1);

namespace App\Module\Bit\Index;

use App\Application\Renderer\JsonRenderer;
use App\Common\Enum\HttpStatus;
use App\Module\Bit\Infrastructure\BitRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

final readonly class ApiIndexAction
{
    public function __construct(private JsonRenderer $renderer, private BitRepository $bits)
    {
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $status = HttpStatus::OK;
            $data = $this->bits->all();
            if (empty($data)) {
                $status = HttpStatus::NO_CONTENT;
            }
        } catch (Throwable $exception) {
            $status = HttpStatus::INTERNAL_SERVER_ERROR;
            $data = ['An unknown error occurred. Sorry about that.'];
            error_log($exception->getMessage());
        }

        return $this->renderer->jsonWithStatus($response, $data, $status);
    }
}
