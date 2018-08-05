<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log settings for your api.
    |
    */

    'log' => env('API_LOG', false),

    /**
     * Handler must implement \Psr\Log\LoggerInterface
     */
    'logger_handler' => \Grizmar\Api\Log\AccessLogger::class,

    'request_format' => '{unique_id}][request][{method}][{url}][{body}',

    'answer_format' => '{unique_id}][answer][{method}][{url}][{body}][{exception_text}',
];
