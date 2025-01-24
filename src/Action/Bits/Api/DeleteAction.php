<?php

declare(strict_types=1);

namespace App\Action\Bits\Api;

use App\Domain\Bit\Repository\BitRepository;
use App\Domain\Exception\DomainRecordNotFoundException;
use App\Infrastructure\Enum\HttpStatus;
use App\Renderer\JsonRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final readonly class DeleteAction
{
    public function __construct(private JsonRenderer $renderer, private BitRepository $bits)
    {
        //
    }

    /**
     * @param array{id: string} $arguments
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $arguments
    ): ResponseInterface {
        try {
            $status = HttpStatus::OK;
            $bit = $this->bits->ofId((int)$arguments['id']);
            $this->bits->destroy($bit);
            $data = ['Bit deleted successfully.'];
        } catch (DomainRecordNotFoundException $exception) {
            $status = HttpStatus::NOT_FOUND;
            $data = [$exception->getMessage()];
        } catch (\Throwable $exception) {
            $status = HttpStatus::INTERNAL_SERVER_ERROR;
            $data = ['An unknown error occurred. Sorry about that.'];
            error_log($exception->getMessage());
        }

        return $this->renderer->jsonWithStatus($response, $data, $status);
    }
}
