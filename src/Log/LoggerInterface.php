<?php

namespace Grizmar\Api\Log;

use Illuminate\Http\Request;
use Grizmar\Api\Response\ResponseInterface;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

interface LoggerInterface extends PsrLoggerInterface
{
    public function addContext(array $context = []): self;

    public function setContextParam($name, $value): self;

    public function getContext(): array;

    public function getContextParam($name, $default = null);

    public function internal(string $text): self;

    public function request(Request $request): void;

    public function answer(ResponseInterface $response): void;
}
