<?php

namespace Grizmar\Api\Exceptions;

use Illuminate\Http\Response;

class ForbiddenException extends BaseException
{
    public function getStatusCode(): int
    {
        return Response::HTTP_FORBIDDEN;
    }
}
