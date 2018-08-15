<?php

namespace Grizmar\Api\Dispatch;

use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Cookie;

abstract class BaseDispatcher implements DispatcherInterface
{
    protected $domain;

    protected $headers;

    protected $params = [];

    protected $cookies = [];

    protected $asArray = false;

    abstract protected function query(string $method, string $uri, array $params, $content);

    public function __construct()
    {
        $this->headers = new HeaderBag();
    }

    public function get(string $uri, array $params = [])
    {
        return $this->query('get', $uri, $params);
    }

    public function post(string $uri, array $params = [], $content = '')
    {
        return $this->query('post', $uri, $params, $content);
    }

    public function put(string $uri, array $params = [], $content = '')
    {
        return $this->query('put', $uri, $params, $content);
    }

    public function patch(string $uri, array $params = [], $content = '')
    {
        return $this->query('patch', $uri, $params, $content);
    }

    public function delete(string $uri, array $params = [], $content = '')
    {
        return $this->query('delete', $uri, $params, $content);
    }

    public function header($key, $value): DispatcherInterface
    {
        $this->headers->set($key, $value);

        return $this;
    }

    public function withHeaders(array $headers): DispatcherInterface
    {
        foreach ($headers as $key => $value) {
            $this->header($key, $value);
        }

        return $this;
    }

    public function cookie(Cookie $cookie): DispatcherInterface
    {
        $this->cookies[] = $cookie;

        return $this;
    }

    public function on(string $domain): DispatcherInterface
    {
        $this->domain = $domain;

        return $this;
    }

    public function with(array $params): DispatcherInterface
    {
        $this->params = $params;

        return $this;
    }

    public function asArray(): DispatcherInterface
    {
        $this->asArray = true;

        return $this;
    }
}
