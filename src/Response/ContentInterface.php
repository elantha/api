<?php

namespace Grizmar\Api\Response;


interface ContentInterface
{
    public function getData(): array;

    public function setData(array $data);

    public function addParam(string $code, $value);

    public function pushData(array $data);

    public function setStatusCode(int $code);

    public function getStatusCode(): int;

    public function header(string $name, $value): self;

    public function withHeaders(array $headers): self;

    public function hasErrors(): bool;

    public function getErrors(): array;

    public function addError($code, string $message);

    public function addErrors($code, array $messages);

    public function addValidationError(string $code, string $message);

    public function setValidationErrors(string $code, array $messages);

    public function getValidationErrors(): array;

    public function hasValidationErrors(): bool;

    public function isValid(): bool;

    public function getAnswer();

    public function getMap(): array;
}
