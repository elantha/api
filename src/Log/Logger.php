<?php

namespace Elantha\Api\Log;

use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\Processor\PsrLogMessageProcessor;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Elantha\Api\Response\JsonResponse;
use Elantha\Api\Response\ResponseInterface;

class Logger extends MonologLogger implements LoggerInterface
{
    public const LOGGER_NAME = 'api';
    public const UNIQUE_ID_LENDTH = 20;

    private const STORAGE_LOG_DIR = 'logs/api/';
    private const FILE_NAME_TEMPLATE = 'access_{DATE}.log';
    private const LOG_FORMAT = "[%datetime%][%level_name%]%message%\n";
    private const DATE_FORMAT = 'Y-m-d';

    private $context = [
        'internal_code' => '',
        'internal_text' => '',
    ];

    public function __construct(?string $name = null)
    {
        if (null === $name) {
            $name = static::LOGGER_NAME;
        }

        $fileName = str_replace('{DATE}', date(static::DATE_FORMAT), static::FILE_NAME_TEMPLATE);
        $filePath = storage_path(static::STORAGE_LOG_DIR . $fileName);

        $handlers[] = (new StreamHandler($filePath, MonologLogger::DEBUG))
            ->setFormatter(new LineFormatter(static::LOG_FORMAT));

        $processors[] = new PsrLogMessageProcessor();

        parent::__construct($name, $handlers, $processors);
    }

    public function addRecord($level, $message, array $context = []): bool
    {
        if (config('api.log', false)) {

            $context = array_replace_recursive($this->getContext(), $context);

            return parent::addRecord($level, $message, $context);
        }

        return false;
    }

    public function setContext(array $context = []): LoggerInterface
    {
        $this->context = array_replace_recursive($this->context, $context);

        return $this;
    }

    public function setContextParam(string $name, $value): LoggerInterface
    {
        array_set($this->context, $name, $value);

        return $this;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function getContextParam(string $name, $default = null)
    {
        return array_get($this->getContext(), $name, $default);
    }

    public function logRequest(Request $request): void
    {
        $this->setContext([
            'url'    => $request->getPathInfo(),
            'method' => $request->getMethod(),
            'query'  => $request->getQueryString(),
        ]);

        if (!$this->getContextParam('unique_id')) {
            $this->setContextParam('unique_id', str_random(static::UNIQUE_ID_LENDTH));
        }

        $localContext = [
            'body' => $this->getRequestBody($request),
        ];

        $this->info(config('api.request_format', ''), $localContext);
    }

    public function logResponse(ResponseInterface $response): void
    {
        $localContext = [
            'body' => $this->getResponseBody($response),
        ];

        $level = $this->getLevelByStatusCode($response->getStatusCode());

        $this->log($level, config('api.answer_format', ''), $localContext);
    }

    protected function getLevelByStatusCode(int $statusCode): int
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

    protected function getRequestBody(Request $request): string
    {
        if ($request->isJson()) {
            $result = json_encode($request->toArray(), JSON_UNESCAPED_UNICODE);
        } else {
            $result = $request->getContent();
        }

        return $result;
    }

    protected function getResponseBody(ResponseInterface $response): string
    {
        if ($response instanceof JsonResponse) {
            $result = json_encode($response->getMap(), JSON_UNESCAPED_UNICODE);
        } else {
            $result = $response->getAnswer()->getContent();
        }

        return $result;
    }
}
