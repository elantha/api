<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Request Configuration
    |--------------------------------------------------------------------------
    |
    | Dispatcher must implement \Grizmar\Api\Internal\DispatcherInterface
    */

    'dispatcher' => \Grizmar\Api\Dispatch\InternalDispatcher::class,

    /*
    |--------------------------------------------------------------------------
    | Response Configuration
    |--------------------------------------------------------------------------
    |
    | Response types as 'Content-Type' => Response type handler
    | Response handler must implement \Grizmar\Api\Response\ResponseInterface
    */

    'response_types' => [
        'application/xml' => \Grizmar\Api\Response\XmlResponse::class,
        'default' => \Grizmar\Api\Response\JsonResponse::class,
    ],

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

    'answer_format' => '{unique_id}][answer][{method}][{url}][{body}][{internal_text}',
];
