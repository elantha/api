<?php

namespace Grizmar\Api\Log;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\Processor\PsrLogMessageProcessor;

class AccessLogger extends Logger
{
    private const STORAGE_LOG_DIR = 'logs/api/';
    private const FILE_NAME_TEMPLATE = 'access_{DATE}.log';
    private const LOG_FORMAT = "[%datetime%][%level_name%][%message%]\n";
    private const DATE_FORMAT = 'Y-m-d';

    public function __construct(string $name, $handlers = [], $processors = [])
    {
        $fileName = str_replace('{DATE}', date(self::DATE_FORMAT), self::FILE_NAME_TEMPLATE);
        $filePath = storage_path(self::STORAGE_LOG_DIR . $fileName);

        $handlers[] = (new StreamHandler($filePath, Logger::DEBUG))
            ->setFormatter(new LineFormatter(self::LOG_FORMAT));

        $processors[] = new PsrLogMessageProcessor();

        parent::__construct($name, $handlers, $processors);
    }
}
