<?php

namespace Grizmar\Api\Response;


class JsonResponse extends BaseResponse
{
    public function getAnswer()
    {
        return \response()->json(
            $this->getMap(),
            $this->getStatusCode(),
            $this->headers->all()
        );
    }
}
