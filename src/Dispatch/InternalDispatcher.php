<?php

namespace Grizmar\Api\Dispatch;

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as RequestFacade;
use Grizmar\Api\Handlers\HandlerInterface;
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

        $response = $this->dispatch($request);

        $this->app->instance('request', $parentRequest);

        resolve(LoggerInterface::class)
            ->setRequestContext($parentRequest);

        return $response;
    }

    protected function dispatch(Request $request)
    {
        try {
            $response = $this->app->handle($request);

            if ($this->asArray) {
                $response = $response->getOriginalContent();
            }
        } catch (\Exception $e) {
            $response = resolve(HandlerInterface::class)
                ->handle($e, $request);

            if ($this->asArray) {
                $response = $response->getMap();
            }
        }

        return $response;
    }

    protected function createRequest($method, $uri, array $params, $content)
    {
        $this->params = array_merge_recursive($this->params, $params);
        $this->content .= $content;

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
}
