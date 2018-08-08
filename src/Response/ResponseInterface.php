<?php

namespace Grizmar\Api\Response;


interface ResponseInterface
{
    public function setStatusCode(int $code);

    public function getStatusCode(): int;

    public function addHeader(string $key, $values);

    public function addHeaders(array $headers);

    public function getData(): array;

    public function setData(array $data);

    public function pushData(array $data);

    public function getParam(string $code, $default = null);

    public function setParam(string $code, $value);

    public function addError($code, string $message);

    public function addErrors($code, array $messages);

    public function hasErrors(): bool;

    public function getErrors(): array;

    public function addValidationError(string $code, string $message);

    public function addValidationErrors(string $code, array $messages);

    public function hasValidationErrors(): bool;

    public function getValidationErrors(): array;

    public function isValid(): bool;

    public function getAnswer();

    public function getMap(): array;
}
