<?php

namespace Grizmar\Api\Handlers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

interface HandlerInterface
{
    public function handle(\Exception $e, Request $request = null): Response;
}
