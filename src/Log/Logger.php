<?php

namespace Grizmar\Api\Log;

use Grizmar\Api\Response\ContentInterface;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Monolog\Logger as MonologLogger;
use Psr\Log\LoggerInterface;

class Logger implements LoggerInterface
{
    const UNIQUE_ID_LENDTH = 20;

    private $handler;

    private $context = [
        'exception_code' => '',
        'exception_text' => '',
    ];

    public function __construct(LoggerInterface $logger)
    {
        $this->handler = $logger;
    }

    public function log($level, $message, array $context = array())
    {
        $context = array_replace_recursive($this->getContext(), $context);
        
        return $this->handler->log($level, $message, $context);
    }

    public function emergency($message, array $context = array())
    {
        return $this->log(MonologLogger::EMERGENCY, $message, $context);
    }

    public function alert($message, array $context = array())
    {
        return $this->log(MonologLogger::ALERT, $message, $context);
    }

    public function critical($message, array $context = array())
    {
        return $this->log(MonologLogger::CRITICAL, $message, $context);
    }

    public function error($message, array $context = array())
    {
        return $this->log(MonologLogger::ERROR, $message, $context);
    }

    public function warning($message, array $context = array())
    {
        return $this->log(MonologLogger::WARNING, $message, $context);
    }

    public function notice($message, array $context = array())
    {
        return $this->log(MonologLogger::NOTICE, $message, $context);
    }

    public function info($message, array $context = array())
    {
        return $this->log(MonologLogger::INFO, $message, $context);
    }

    public function debug($message, array $context = array())
    {
        return $this->log(MonologLogger::DEBUG, $message, $context);
    }

    public function addContext(array $context = []): self
    {
        $this->context = array_replace_recursive($this->context, $context);

        return $this;
    }

    public function setContextParam($name, $value): self
    {
        array_set($this->context, $name, $value);

        return $this;
    }

    public function request(Request $request): void
    {
        $this->addContext([
            'url'    => $request->getPathInfo(),
            'method' => $request->getMethod(),
        ]);

        if (!$this->getContextParam('unique_id')) {
            $this->setContextParam('unique_id', str_random(self::UNIQUE_ID_LENDTH));
        }

        $localContext = [
            'body' => json_encode($request->toArray(), JSON_UNESCAPED_UNICODE),
        ];

        $this->info(config('api.request_format', ''), $localContext);
    }

    public function answer(ContentInterface $response): void
    {
        $localContext = [
            'body' => json_encode($response->getMap(), JSON_UNESCAPED_UNICODE),
        ];

        $level = $this->getLevel($response->getStatusCode());

        $this->log($level, config('api.answer_format', ''), $localContext);
    }

    protected function getContext(): array
    {
        return $this->context;
    }

    protected function getContextParam($name, $default = null)
    {
        return array_get($this->getContext(), $name, $default);
    }

    protected function getLevel($statusCode): int
    {
        if ($statusCode >= Response::HTTP_INTERNAL_SERVER_ERROR) {
            $result = MonologLogger::CRITICAL;
        } elseif ($statusCode >= Response::HTTP_BAD_REQUEST) {
            $result = MonologLogger::ERROR;
        } else {
            $result = MonologLogger::INFO;
        }

        return $result;
    }
}