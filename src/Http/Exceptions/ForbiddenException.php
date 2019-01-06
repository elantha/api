<?php

namespace Elantha\Api\Http\Exceptions;

use Illuminate\Http\Response;

class ForbiddenException extends BaseHttpException
{
    public function getStatusCode(): int
    {
        return Response::HTTP_FORBIDDEN;
    }
}
