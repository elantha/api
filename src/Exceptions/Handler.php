<?php

namespace Grizmar\Api\Exceptions;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Grizmar\Api\Response\ResponseInterface;
use Grizmar\Api\Log\LoggerInterface;

class Handler
{
    public static function render(Request $request, \Exception $e)
    {
        if ($e instanceof BaseException) {
            $response = static::getApiResponse($e);
        } elseif ($e instanceof ValidationException) {
            $response = static::getValidationErrorResponse($e);
        } else {
            $response = static::getInternalErrorResponse();

            self::log($e);
        }

        return response()->rest($response);
    }

    protected static function getApiResponse(BaseException $e): ResponseInterface
    {
        $response = $e->getResponse();

        if (!($e instanceof EmptyException)) {
            $response
                ->addError($e->getCode(), $e->getMessage())
                ->setStatusCode($e->getStatusCode());
        }

        return $response;
    }

    protected static function getValidationErrorResponse(ValidationException $e): ResponseInterface
    {
        $response = resolve(ResponseInterface::class);

        $errors = $e->validator->errors()->getMessages();

        $response->addValidationErrors($errors);

        $response->setStatusCode($e->status);

        return $response;
    }

    protected static function getInternalErrorResponse(): ResponseInterface
    {
        $response = resolve(ResponseInterface::class);

        $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);

        return $response;
    }

    protected static function log(\Exception $e): void
    {
        resolve(LoggerInterface::class)
            ->addContext([
                'internal_code' => $e->getCode(),
                'internal_text' => $e->getMessage(),
            ]);
    }
}
