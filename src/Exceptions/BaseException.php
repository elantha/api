<?php

namespace Grizmar\Api\Exceptions;

use Grizmar\Api\Messages\KeeperInterface;
use Grizmar\Api\Response\ContentInterface;

abstract class BaseException extends \Exception
{
    private $response;

    public static function make($code = 0, array $context = []): self
    {
        $message = resolve(KeeperInterface::class)
            ->getMessage($code, $context);

        return new static($message, $code);
    }

    public function setResponse(ContentInterface $response): self
    {
        $this->response = $response;

        return $this;
    }

    public function getResponse(): ContentInterface
    {
        if (empty($this->response)) {
            $this->response = resolve(ContentInterface::class);
        }

        return $this->response;
    }

    abstract public function getStatusCode(): int;
}
