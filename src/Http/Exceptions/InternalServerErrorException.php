<?php

namespace Grizmar\Api\Http\Exceptions;

use Illuminate\Http\Response;

class InternalServerErrorException extends BaseHttpException
{
    public function getStatusCode(): int
    {
        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }
}
