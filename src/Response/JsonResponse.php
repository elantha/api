<?php

namespace Elantha\Api\Response;

use Symfony\Component\HttpFoundation\Response as HttpResponse;

class JsonResponse extends BaseResponse
{
    public const CONTENT_TYPE = 'application/json';

    public function getAnswer(): HttpResponse
    {
        return \response()->json(
            $this->getMap(),
            $this->getStatusCode(),
            $this->headers->all()
        );
    }
}
