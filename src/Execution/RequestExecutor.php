<?php

namespace Elantha\Api\Execution;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Elantha\Api\Response\ResponseInterface;
use Elantha\Api\Log\LoggerInterface;
use Elantha\Api\Handlers\HandlerInterface;

trait RequestExecutor
{
    use ValidatesRequests;

    /** @var Request */
    protected $request;
    /** @var ResponseInterface */
    protected $response;
    /** @var LoggerInterface */
    protected $logger;

    protected $validationRules = [];

    protected function init(Request $request): void
    {
        $this->request = $request;

        $this->response = resolve(ResponseInterface::class);

        $this->logger = resolve(LoggerInterface::class);

        $this->validationRules = $this->initValidationRules();
    }

    protected function invoke(StrategyInterface $strategy, Request $request = null): ResponseInterface
    {
        $request = $request ?? $this->request;

        try {
            $response = $strategy->invoke($request);
        } catch (\Exception $e) {
            /** @var HandlerInterface $errorHandler */
            $errorHandler = resolve(HandlerInterface::class);
            $response = $errorHandler->process($e, $request);

            $this->logResponse($response);
        }

        return $response;
    }

    protected function logRequest(Request $request): void
    {
        $this->logger->logRequest($request);
    }

    protected function logResponse(ResponseInterface $response): void
    {
        $this->logger->logResponse($response);
    }

    protected function initValidationRules(): array
    {
        return [];
    }

    protected function hasErrors(): bool
    {
        return $this->response->isValid();
    }

    protected function input($key, $default = null)
    {
        return $this->request->input($key, $default);
    }

    protected function output($key, $value): self
    {
        $this->response->setParam($key, $value);

        return $this;
    }

    protected function error(string $code, ?string $message = null, array $context = []): self
    {
        $this->response->addError($code, $message, $context);

        return $this;
    }

    protected function log($level, string $message, array $context = []): void
    {
        $this->logger->log($level, $message, $context);
    }
}
