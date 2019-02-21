<?php

namespace Elantha\Api\Execution;

use Illuminate\Http\Request;
use Elantha\Api\Response\ResponseInterface;

interface StrategyInterface
{
    public function invoke(Request $request): ResponseInterface;
}
