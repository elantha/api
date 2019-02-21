<?php

namespace Elantha\Api\Execution;

use Elantha\Api\Response\ResponseInterface;
use Illuminate\Http\Request;

abstract class Strategy implements StrategyInterface
{
    use RequestExecutor;

    abstract protected function execute();

    final public function invoke(Request $request): ResponseInterface
    {
        $this->init($request);

        $this->logRequest($this->request);

        $this->validate($this->request, $this->validationRules ?? []);

        $this->execute();

        $this->logResponse($this->response);

        return $this->response;
    }
}
