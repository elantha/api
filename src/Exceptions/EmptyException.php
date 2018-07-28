<?php

namespace Grizmar\Api\Exceptions;


class EmptyException extends BaseException
{
    public function getStatusCode(): int
    {
        return 0;
    }
}
