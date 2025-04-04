<?php

declare(strict_types=1);

namespace App\Module\Bit\Show;

use App\Application\Renderer\JsonRenderer;
use App\Common\Enum\HttpStatus;
use App\Domain\Exception\DomainRecordNotFoundException;
use App\Module\Bit\Infrastructure\BitRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

final readonly class ApiShowAction
{
    public function __construct(private JsonRenderer $renderer, private BitRepository $bits)
    {
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array<string, string> $arguments
     * @return ResponseInterface
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $arguments,
    ): ResponseInterface {
        try {
            $status = HttpStatus::OK;
            $data = $this->bits->ofId((int)$arguments['sqid']);
        } catch (DomainRecordNotFoundException $exception) {
            $status = HttpStatus::NOT_FOUND;
            $data = [$exception->getMessage()];
        } catch (Throwable $exception) {
            $status = HttpStatus::INTERNAL_SERVER_ERROR;
            $data = ['An unknown error occurred. Sorry about that.'];
            error_log($exception->getMessage());
        }

        return $this->renderer->jsonWithStatus($response, $data, $status);
    }
}
