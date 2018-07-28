<?php

namespace Grizmar\Api\Exceptions;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Grizmar\Api\Response\ContentInterface;

class Handler
{
    public static function render(Request $request, \Exception $e)
    {
        report($e); // log exception

        if ($e instanceof BaseException) {

            $response = $e->getResponse();

            if (!($e instanceof EmptyException)) {
                $response
                    ->addError($e->getCode(), $e->getMessage())
                    ->setStatusCode($e->getStatusCode());
            }

        } else {
            $response = self::getInternalErrorResponse();
        }

        return response()->rest($response);
    }

    private static function getInternalErrorResponse(): ContentInterface
    {
        $response = resolve(ContentInterface::class);

        $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);

        return $response;
    }
}
