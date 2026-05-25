<?php

declare(strict_types=1);

namespace App\Module\Snap\Application\Action\Api;

use App\Application\Renderer\JsonRenderer;
use App\Common\Domain\HttpStatus;
use App\Infrastructure\Exception\DomainRecordNotFoundException;
use App\Module\Snap\Infrastructure\SnapRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class DeleteSnapAction
{
    public function __construct(private readonly JsonRenderer $renderer, private readonly SnapRepository $repository)
    {
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array{sqid: string} $arguments
     *
     * @return ResponseInterface
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $arguments,
    ): ResponseInterface {
        try {
            $status = HttpStatus::OK;
            $thing = $this->repository->ofId((int)$arguments['sqid']);
            $this->repository->destroy($thing);
            $data = ['Album deleted successfully.'];
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
