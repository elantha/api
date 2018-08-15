<?php

namespace Grizmar\Api\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Grizmar\Api\Response\ResponseInterface;
use Grizmar\Api\Validators\RequestValidator;
use Grizmar\Api\Dispatch\DispatcherInterface;
use Grizmar\Api\Log\LoggerInterface;

class BaseController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, RequestValidator;

    protected $response;
    protected $request;
    protected $logger;

    final public function __construct(
        Request $request,
        ResponseInterface $response,
        LoggerInterface $logger
    ){
        $this->response = $response;
        $this->request = $request;
        $this->logger = $logger;

        $this->requestLog();

        $this->initValidationRules();

        $this->validate($request, $this->validationRules);
    }

    final protected function hasErrors(): bool
    {
        return $this->response->isValid();
    }

    final protected function input($key, $default = null)
    {
        return $this->request->input($key, $default);
    }

    protected function initValidationRules(): self
    {
    }

    protected function requestLog(): void
    {
        $this->logger->request($this->request);
    }

    protected function log($level, string $message, array $context = [])
    {
        return $this->logger->log($level, $message, $context);
    }

    protected function dispatcher(): DispatcherInterface
    {
        return resolve(DispatcherInterface::class);
    }
}
