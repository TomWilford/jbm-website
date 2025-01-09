<?php

namespace App\Infrastructure\Enum;

enum HttpStatus: int
{
    /** Success */
    case OK = 200;
    case CREATED = 201;
    case NO_CONTENT = 204;

    /** Redirect */
    case MOVED_PERMANENTLY = 301;

    /** Client Error */
    case BAD_REQUEST = 400;
    case UNAUTHORIZED = 401;
    case FORBIDDEN = 403;
    case NOT_FOUND = 404;
    case METHOD_NOT_ALLOWED = 405;

    /** Server Error */
    case INTERNAL_SERVER_ERROR = 500;

    public static function isSuccess(self $status): bool
    {
        return match ($status) {
            self::OK, self::CREATED => true,
            default => false
        };
    }
}
