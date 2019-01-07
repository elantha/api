<?php

namespace Elantha\Api\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
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
    ){
        $this->response = $response;
        $this->request = $request;
        $this->logger = $logger;

        $this->requestLog();

        $this->validationRules = $this->initValidationRules();
    }

    private function requestLog(): void
    {
        $this->logger->request($this->request);
    }

    protected function initValidationRules(): array
    {
        return [];
    }

    public function callAction($method, $parameters)
    {
        $this->validate($this->request, $this->validationRules[$method] ?? []);

        return parent::callAction($method, $parameters);
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

    protected function error($code, $message): self
    {
        $this->response->addError($code, $message);

        return $this;
    }

    protected function log($level, string $message, array $context = [])
    {
        return $this->logger->log($level, $message, $context);
    }
}
