<?php

namespace Grizmar\Api\Exceptions;

use Illuminate\Http\Response;

class NotFoundException extends BaseException
{
    public function getStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }
}
