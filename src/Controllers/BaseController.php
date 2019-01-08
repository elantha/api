<?php

namespace Elantha\Api\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Elantha\Api\Response\ResponseInterface;
use Elantha\Api\Log\LoggerInterface;

class BaseController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $response;
    protected $request;
    protected $logger;
    protected $validationRules = [];

    final public function __construct(
        Request $request,
        ResponseInterface $response,
        LoggerInterface $logger
    ) {
        $this->response = $response;
        $this->request = $request;
        $this->logger = $logger;

        $this->logRequest();

        $this->validationRules = $this->initValidationRules();
    }

    public function callAction($method, $parameters): Response
    {
        $this->validate($this->request, $this->validationRules[$method] ?? []);

        return parent::callAction($method, $parameters);
    }

    protected function logRequest(): void
    {
        $this->logger->logRequest($this->request);
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
