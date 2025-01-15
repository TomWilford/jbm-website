<?php

declare(strict_types=1);

namespace App\Renderer;

use App\Infrastructure\Enum\HttpStatus;
use Psr\Http\Message\ResponseInterface;

final class JsonRenderer
{
    public function json(
        ResponseInterface $response,
        mixed $data = null,
    ): ResponseInterface {
        $response = $response->withHeader('Content-Type', 'application/json');

        $response->getBody()->write(
            (string)json_encode(
                $data,
                JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR
            )
        );

        return $response;
    }

    public function jsonWithStatus(
        ResponseInterface $response,
        mixed $data = [],
        HttpStatus $httpStatus = HttpStatus::OK
    ): ResponseInterface {
        $response = $response->withHeader('Content-Type', 'application/json');
        $status = HttpStatus::isSuccess($httpStatus) ? 'success' : 'error';
        $key = HttpStatus::isSuccess($httpStatus) ? 'data' : 'messages';

        $response->getBody()->write(
            (string)json_encode(
                [
                    'status' => $status,
                    $key => $data,
                ],
                JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR
            )
        );

        return $response->withStatus($httpStatus->value);
    }
}
