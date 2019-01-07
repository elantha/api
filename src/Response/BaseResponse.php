<?php

namespace Elantha\Api\Response;

use Elantha\Api\Messages\KeeperInterface;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class BaseResponse implements ResponseInterface
{
    protected $data = [];

    protected $errors = [];

    protected $validationErrors = [];

    protected $status = HttpResponse::HTTP_OK;

    /* @var HeaderBag $headers */
    protected $headers;

    public function __construct()
    {
        $this->headers = new HeaderBag();
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): ResponseInterface
    {
        $this->data = $data;

        return $this;
    }

    public function pushData(array $data): ResponseInterface
    {
        $this->data = array_replace_recursive($this->data, $data);

        return $this;
    }

    public function setParam(string $code, $value): ResponseInterface
    {
        array_set($this->data, $code, $value);

        return $this;
    }

    public function getParam(string $code, $default = null)
    {
        return array_get($this->data, $code, $default);
    }

    public function addError(string $code, ?string $message = null, array $context = []): ResponseInterface
    {
        if (null === $message) {
            /** @var KeeperInterface $keeper */
            $keeper = resolve(KeeperInterface::class);
            $message = $keeper->getMessage($code, $context);
        }
        
        $this->errors[$code] = $message;

        return $this;
    }

    public function addErrors(array $errors): ResponseInterface
    {
        foreach ($errors as $code => $message) {
            $this->addError($code, $message);
        }

        return $this;
    }

    public function addValidationError(string $code, $message): ResponseInterface
    {
        $this->validationErrors[$code] = $message;

        return $this;
    }

    public function addValidationErrors(array $errors): ResponseInterface
    {
        foreach ($errors as $code => $message) {
            $this->addValidationError($code, $message);
        }

        return $this;
    }

    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }

    public function getErrors(): array
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

    public function getStatusCode(): int
    {
        return $this->status;
    }

    public function setStatusCode(int $code): ResponseInterface
    {
        $this->status = $code;

        return $this;
    }

    public function addHeader(string $key, $value): ResponseInterface
    {
        $this->headers->set($key, $value);

        return $this;
    }

    public function addHeaders(array $headers): ResponseInterface
    {
        foreach ($headers as $key => $value) {
            $this->addHeader($key, $value);
        }

        return $this;
    }

    public function isValid(): bool
    {
        return !$this->hasErrors() && !$this->hasValidationErrors();
    }

    public function getAnswer(): HttpResponse
    {
        return \response($this->getMap(), $this->getStatusCode())
            ->withHeaders($this->headers);
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
