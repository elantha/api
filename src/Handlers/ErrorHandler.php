<?php

namespace Grizmar\Api\Handlers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Grizmar\Api\Response\ResponseInterface;
use Grizmar\Api\Exceptions\BaseException;
use Grizmar\Api\Log\LoggerInterface;

class ErrorHandler implements HandlerInterface
{
    public function handle(\Exception $e, Request $request = null): HttpResponse
    {
        if ($e instanceof BaseException) {
            $response = $this->getApiResponse($e);
        } elseif ($e instanceof ValidationException) {
            $response = $this->getValidationErrorResponse($e);
        } else {
            $response = $this->getInternalErrorResponse();

            $this->addMessageToLog($e);
        }

        return response()->rest($response);
    }

    protected function getApiResponse(BaseException $e): ResponseInterface
    {
        $response = $e->getResponse();

        if (!($e instanceof EmptyException)) {
            $response
                ->addError($e->getCode(), $e->getMessage())
                ->setStatusCode($e->getStatusCode());
        }

        return $response;
    }

    protected function getValidationErrorResponse(ValidationException $e): ResponseInterface
    {
        $response = resolve(ResponseInterface::class);

        $errors = $e->validator->errors()->getMessages();

        $response->addValidationErrors($errors);

        $response->setStatusCode($e->status);

        return $response;
    }

    protected function getInternalErrorResponse(): ResponseInterface
    {
        $response = resolve(ResponseInterface::class);

        $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);

        return $response;
    }

    protected function addMessageToLog(\Exception $e): void
    {
        resolve(LoggerInterface::class)
            ->addContext([
                'internal_code' => $e->getCode(),
                'internal_text' => $e->getMessage(),
            ]);
    }
}
