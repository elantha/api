<?php

namespace Grizmar\Api\Exceptions;

use Illuminate\Http\Response;

class UnauthorizedException extends BaseException
{
    public function getStatusCode(): int
    {
        return Response::HTTP_UNAUTHORIZED;
    }
}
