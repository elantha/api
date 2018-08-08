<?php

namespace Grizmar\Api\Response;

use Symfony\Component\HttpFoundation\Response as HttpResponse;

class BaseResponse implements ResponseInterface
{
    protected $data = [];

    protected $errors = [];

    protected $validationErrors = [];

    protected $status = HttpResponse::HTTP_OK;

    protected $headers = [];

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function pushData(array $data): self
    {
        $this->data = array_replace_recursive($this->data, $data);

        return $this;
    }

    public function setParam(string $code, $value): self
    {
        array_set($this->data, $code, $value);

        return $this;
    }

    public function getParam(string $code, $default = null)
    {
        return array_get($this->data, $code, $default);
    }

    final public function addError($code, string $message): self
    {
        $this->errors[$code] = $message;

        return $this;
    }

    final public function addErrors($code, array $messages): self
    {
        $this->errors[$code] = $messages;

        return $this;
    }

    final public function addValidationError(string $code, string $message): self
    {
        $this->validationErrors[$code][] = $message;

        return $this;
    }

    final public function addValidationErrors(string $code, array $messages): self
    {
        $this->validationErrors[$code] = $messages;

        return $this;
    }

    final public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }

    final public function getErrors(): array
    {
        return $this->errors;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    public function hasValidationErrors(): bool
    {
        return !empty($this->validationErrors);
    }

    final public function getStatusCode(): int
    {
        return $this->status;
    }

    final public function setStatusCode(int $code): self
    {
        $this->status = $code;

        return $this;
    }

    final public function addHeader(string $key, $value): self
    {
        if (!empty($key)) {
            $this->headers[$key] = $value;
        }

        return $this;
    }

    final public function addHeaders(array $headers): self
    {
        foreach ($headers as $key => $value) {
            $this->addHeader($key, $value);
        }

        return $this;
    }

    final public function isValid(): bool
    {
        return !$this->hasErrors() && !$this->hasValidationErrors();
    }

    public function getAnswer()
    {
        $response = \response($this->getMap(), $this->getStatusCode());

        if (!empty($this->headers)) {
            $response->withHeaders($this->headers);
        }

        return $response;
    }

    public function getMap(): array
    {
        return [
            'status'            => $this->getStatusCode(),
            'errors'            => $this->getErrors(),
            'validation_errors' => $this->getValidationErrors(),
            'data'              => $this->data,
        ];
    }
}
