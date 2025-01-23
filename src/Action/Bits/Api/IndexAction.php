<?php

declare(strict_types=1);

namespace App\Action\Bits\Api;

use App\Domain\Bit\Repository\BitRepository;
use App\Infrastructure\Enum\HttpStatus;
use App\Renderer\JsonRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final readonly class IndexAction
{
    public function __construct(private JsonRenderer $renderer, private BitRepository $bits)
    {
        //
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $status = HttpStatus::OK;
            $data = $this->bits->all();
            if (empty($data)) {
                $status = HttpStatus::NO_CONTENT;
            }
        } catch (\Throwable $exception) {
            $status = HttpStatus::INTERNAL_SERVER_ERROR;
            $data = ['An unknown error occurred. Sorry about that.'];
            error_log($exception->getMessage());
        }

        return $this->renderer->jsonWithStatus($response, $data, $status);
    }
}
