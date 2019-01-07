<?php

namespace Elantha\Api\Handlers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

interface HandlerInterface
{
    public function handle(\Throwable $e, Request $request = null): Response;
}
