<?php

namespace Elantha\Api\Http\Exceptions;

use Illuminate\Http\Response;

class UnauthorizedException extends BaseHttpException
{
    public function getStatusCode(): int
    {
        return Response::HTTP_UNAUTHORIZED;
    }
}
