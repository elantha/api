<?php

namespace Elantha\Api\Handlers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Elantha\Api\Response\ResponseInterface;
use Elantha\Api\Http\Exceptions\BaseHttpException;
use Elantha\Api\Http\Exceptions\EmptyException;
use Elantha\Api\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ErrorHandler implements HandlerInterface
{
    public function handle(\Throwable $e, Request $request = null): Response
    {
        if ($e instanceof HttpException) {
            $response = $this->getKernelHttpResponse($e);
            $this->logInternalError($e, $request);
        }
        elseif ($e instanceof BaseHttpException) {
            $response = $this->getApiResponse($e);
        }
        elseif ($e instanceof ValidationException) {
            $response = $this->getValidationErrorResponse($e);
        }
        else {
            $response = $this->getInternalErrorResponse();
            $this->logInternalError($e);
        }

        return response()->rest($response);
    }

    protected function getKernelHttpResponse(HttpException $e): ResponseInterface
    {
        /* @var ResponseInterface $response */
        $response = resolve(ResponseInterface::class);

        $response->setStatusCode($e->getStatusCode());
        $response->addHeaders($e->getHeaders());
        $response->addError($e->getCode(), $e->getMessage());

        return $response;
    }

    protected function getApiResponse(BaseHttpException $e): ResponseInterface
    {
        $response = $e->getResponse();

        if (!($e instanceof EmptyException)) {

            $response->setStatusCode($e->getStatusCode());

            if ($e->getMessage()) {
                $response->addError($e->getCode(), $e->getMessage());
            }
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

    protected function logInternalError(\Throwable $e, Request $request = null): void
    {
        /* @var LoggerInterface $logger */
        $logger = resolve(LoggerInterface::class);

        $logger->addContext([
            'internal_code' => $e->getCode(),
            'internal_text' => $e->getMessage(),
        ]);

        if ($request) {
            $logger->request($request);
        }
    }
}
