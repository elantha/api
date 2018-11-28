<?php

namespace Grizmar\Api\Http\Exceptions;

use Illuminate\Http\Response;

class NotFoundException extends BaseHttpException
{
    public function getStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }
}
