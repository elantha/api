<?php

namespace Grizmar\Api\Response;

use Symfony\Component\HttpFoundation\Response as HttpResponse;

interface ResponseInterface
{
    public function setStatusCode(int $code): self;

    public function getStatusCode(): int;

    public function addHeader(string $key, $values): self;

    public function addHeaders(array $headers): self;

    public function getData(): array;

    public function setData(array $data): self;

    public function pushData(array $data): self;

    public function getParam(string $code, $default = null);

    public function setParam(string $code, $value): self;

    public function addError($code, $message): self;

    public function addErrors(array $errors): self;

    public function hasErrors(): bool;

    public function getErrors(): array;

    public function addValidationError(string $code, $message): self;

    public function addValidationErrors(array $errors): self;

    public function hasValidationErrors(): bool;

    public function getValidationErrors(): array;

    public function isValid(): bool;

    public function getAnswer(): HttpResponse;

    public function getMap(): array;
}
