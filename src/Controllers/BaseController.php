<?php

namespace Grizmar\Api\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Grizmar\Api\Response\ContentInterface;
use Grizmar\Api\Validators\RequestValidator;
use Grizmar\Api\Log\LoggerInterface;

class BaseController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, RequestValidator;

    protected $response;
    protected $request;
    protected $logger;

    final public function __construct(
        Request $request,
        ContentInterface $response,
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
}
