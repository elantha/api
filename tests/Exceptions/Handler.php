<?php

namespace Grizmar\Api\Tests\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Grizmar\Api\Handlers\HandlerInterface;

/**
 * Class Handler
 * Error handler example
 * @package Grizmar\Api\Tests\Exceptions
 */
class Handler extends ExceptionHandler
{
    public function render($request, \Exception $exception)
    {
        if ($request->is('test/*')) {
            return resolve(HandlerInterface::class)
                ->handle($exception, $request);
        }

        return parent::render($request, $exception);
    }
}
