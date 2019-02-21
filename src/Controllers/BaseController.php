<?php

namespace Elantha\Api\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Elantha\Api\Execution\RequestExecutor;

class BaseController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, RequestExecutor;

    final public function __construct(Request $request)
    {
        $this->init($request);

        $this->logRequest($this->request);
    }

    public function callAction($method, $parameters): Response
    {
        $this->validate($this->request, $this->validationRules[$method] ?? []);

        return parent::callAction($method, $parameters);
    }
}
