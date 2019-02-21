<?php

namespace Elantha\Api\Handlers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Elantha\Api\Response\ResponseInterface;

interface HandlerInterface
{
    public function handle(\Throwable $e, Request $request = null): Response;

    public function process(\Throwable $e, Request $request = null): ResponseInterface;
}
