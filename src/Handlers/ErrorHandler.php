<?php

namespace Grizmar\Api\Handlers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Grizmar\Api\Response\ResponseInterface;
use Grizmar\Api\Http\Exceptions\BaseHttpException;
use Grizmar\Api\Http\Exceptions\EmptyException;
use Grizmar\Api\Log\LoggerInterface;

class ErrorHandler implements HandlerInterface
{
    public function handle(\Throwable $e, Request $request = null): Response
    {
        if ($e instanceof BaseHttpException) {
            $response = $this->getApiResponse($e);
        } elseif ($e instanceof ValidationException) {
            $response = $this->getValidationErrorResponse($e);
        } else {
            $response = $this->getInternalErrorResponse();

            $this->addMessageToLog($e);
        }

        return response()->rest($response);
    }

    protected function getApiResponse(BaseHttpException $e): ResponseInterface
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
        /* @var ResponseInterface $response */
        $response = resolve(ResponseInterface::class);

        $errors = $e->validator->errors()->getMessages();

        $response->addValidationErrors($errors);

        $response->setStatusCode($e->status);

        return $response;
    }

    protected function getInternalErrorResponse(): ResponseInterface
    {
        /* @var ResponseInterface $response */
        $response = resolve(ResponseInterface::class);

        $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);

        return $response;
    }

    protected function addMessageToLog(\Throwable $e): void
    {
        /* @var LoggerInterface $logger */
        $logger = resolve(LoggerInterface::class);

        $logger->addContext([
            'internal_code' => $e->getCode(),
            'internal_text' => $e->getMessage(),
        ]);
    }
}
