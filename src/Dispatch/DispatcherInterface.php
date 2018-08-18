<?php

namespace Grizmar\Api\Dispatch;

use Symfony\Component\HttpFoundation\Cookie;

interface DispatcherInterface
{
    public function get(string $uri, array $params = []);

    public function post(string $uri, array $params = [], $content = '');

    public function put(string $uri, array $params = [], $content = '');

    public function patch(string $uri, array $params = [], $content = '');

    public function delete(string $uri, array $params = [], $content = '');

    public function header($key, $value): DispatcherInterface;

    public function withHeaders(array $headers): DispatcherInterface;

    public function cookie(Cookie $cookie): DispatcherInterface;

    public function on(string $domain): DispatcherInterface;

    public function withParams(array $params): DispatcherInterface;

    public function withContent($content): DispatcherInterface;

    public function json(string $content): DispatcherInterface;
}
