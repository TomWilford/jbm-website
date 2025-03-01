<?php

declare(strict_types=1);

namespace App\Action\Things\Api;

use App\Domain\Exception\DomainRecordNotFoundException;
use App\Domain\Thing\Repository\ThingRepository;
use App\Infrastructure\Enum\HttpStatus;
use App\Renderer\JsonRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

final readonly class DeleteAction
{
    public function __construct(
        private JsonRenderer $renderer,
        private ThingRepository $things,
    ) {
    }

    /**
     * @param array{id: string} $arguments
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $arguments,
    ): ResponseInterface {
        try {
            $status = HttpStatus::OK;
            $thing = $this->things->ofId((int)$arguments['sqid']);
            $this->things->destroy($thing);
            $data = ['Thing deleted successfully.'];
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
