<?php

declare(strict_types=1);

namespace App\Action\Things\Api;

use App\Domain\Thing\Repository\ThingRepository;
use App\Infrastructure\Enum\HttpStatus;
use App\Renderer\JsonRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final readonly class IndexAction
{
    public function __construct(private JsonRenderer $renderer, private ThingRepository $things)
    {
        //
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $status = HttpStatus::OK;
        try {
            $data = $this->things->all();
            if (empty($data)) {
                $status = HttpStatus::NO_CONTENT;
            }
        } catch (\Throwable $exception) {
            $status = HttpStatus::INTERNAL_SERVER_ERROR;
            $data = ['An error occurred. Sorry about that.'];
            error_log($exception->getMessage());
        }

        return $this->renderer->jsonWithStatus(
            $response,
            $data,
            $status
        );
    }
}
