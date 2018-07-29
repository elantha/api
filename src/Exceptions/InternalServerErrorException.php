<?php

namespace Grizmar\Api\Exceptions;

use Illuminate\Http\Response;

class InternalServerErrorException extends BaseException
{
    public function getStatusCode(): int
    {
        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }
}
