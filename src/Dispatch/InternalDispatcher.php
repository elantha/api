<?php

namespace Grizmar\Api\Dispatch;

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as RequestFacade;
use Symfony\Component\HttpFoundation\Response;
use Grizmar\Api\Response\ResponseInterface;
use Grizmar\Api\Exceptions\Handler;
use Grizmar\Api\Log\LoggerInterface;

class InternalDispatcher extends BaseDispatcher
{
    private $app;

    public function __construct(Application $app)
    {
        parent::__construct();

        $this->app = $app;
    }

    protected function query($method, $uri, array $params = [], $content = '')
    {
        $parentRequest = clone request();

        $request = $this->createRequest($method, $uri, $params, $content);

        RequestFacade::clearResolvedInstance('request');

        try {
            $response = $this->createResponse(
                $this->app->handle($request)
            );

            $this->app->instance('request', $parentRequest);

            resolve(LoggerInterface::class)
                ->setRequestContext($parentRequest);

        } catch (\Exception $e) {

            $response = Handler::render($request, $e);

            if ($this->asArray) {
                $response = $response->getMap();
            }
        }

        return $response;
    }

    protected function createRequest($method, $uri, array $params, $content)
    {
        $this->params = array_merge_recursive($this->params, $params);

        $request = Request::create(
            $uri,
            $method,
            $this->params,
            $this->cookies,
            [],
            $this->app['request']->server->all(),
            $content
        );

        foreach ($this->headers as $header => $value) {
            $request->headers->set($header, $value);
        }

        return $request;
    }

    protected function createResponse(Response $httpResponse): ResponseInterface
    {
        $content = $httpResponse->getOriginalContent();

        if (!$this->asArray) {
            $response = resolve(ResponseInterface::class);
            $response->load($content, $httpResponse->headers->all());
        } else {
            $response = $content;
        }

        return $response;
    }
}
