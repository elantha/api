<?php

namespace Grizmar\Api\Http\Exceptions;

use Illuminate\Http\Response;

class MethodNotAllowedException extends BaseHttpException
{
    public function getStatusCode(): int
    {
        return Response::HTTP_METHOD_NOT_ALLOWED;
    }
}
