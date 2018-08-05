<?php

namespace Grizmar\Api\Exceptions;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Grizmar\Api\Response\ContentInterface;
use Grizmar\Api\Log\Logger;

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
        }

        self::addToLog($e);

        return response()->rest($response);
    }

    protected static function getApiResponse(BaseException $e): ContentInterface
    {
        $response = $e->getResponse();

        if (!($e instanceof EmptyException)) {
            $response
                ->addError($e->getCode(), $e->getMessage())
                ->setStatusCode($e->getStatusCode());
        }

        return $response;
    }

    protected static function getValidationErrorResponse(ValidationException $e): ContentInterface
    {
        $response = resolve(ContentInterface::class);

        $errors = $e->validator->errors()->getMessages();

        foreach($errors as $fieldName => $fieldMessages) {
            $response->setValidationErrors($fieldName, $fieldMessages);
        }

        $response->setStatusCode($e->status);

        return $response;
    }

    protected static function getInternalErrorResponse(): ContentInterface
    {
        $response = resolve(ContentInterface::class);

        $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);

        return $response;
    }

    protected static function addToLog(\Exception $e): void
    {
        if (config('api.log', false)) {

            $context = [
                'exception_code' => $e->getCode(),
                'exception_text' => $e->getMessage(),
            ];

            resolve(Logger::class)->addContext($context);
        }
    }
}
