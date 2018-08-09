<?php

namespace Grizmar\Api\Exceptions;

use Grizmar\Api\Messages\KeeperInterface;
use Grizmar\Api\Response\ResponseInterface;

abstract class BaseException extends \Exception
{
    private $response;

    public static function make($code = 0, array $context = []): self
    {
        $message = resolve(KeeperInterface::class)
            ->getMessage($code, $context);

        return new static($message, $code);
    }

    public function setResponse(ResponseInterface $response): self
    {
        $this->response = $response;

        return $this;
    }

    public function getResponse(): ResponseInterface
    {
        if (empty($this->response)) {
            $this->response = resolve(ResponseInterface::class);
        }

        return $this->response;
    }

    abstract public function getStatusCode(): int;
}
