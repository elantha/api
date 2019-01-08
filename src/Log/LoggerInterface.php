<?php

namespace Elantha\Api\Log;

use Illuminate\Http\Request;
use Elantha\Api\Response\ResponseInterface;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

interface LoggerInterface extends PsrLoggerInterface
{
    public function setContext(array $context = []): self;

    public function setContextParam(string $name, $value): self;

    public function getContext(): array;

    public function getContextParam(string $name, $default = null);

    public function logRequest(Request $request): void;

    public function logAnswer(ResponseInterface $response): void;
}
