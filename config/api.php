<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Response Configuration
    |--------------------------------------------------------------------------
    |
    | Response types as 'Content-Type' => Response type handler
    | NOTE: Response handler must implement \Grizmar\Api\Response\ResponseInterface
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
    | NOTE: Handler must implement \Psr\Log\LoggerInterface
    */

    'log' => env('API_LOG', false),

    'logger_handler' => \Grizmar\Api\Log\AccessLogger::class,

    'request_format' => '{unique_id}][request][{method}][{url}][{body}',

    'answer_format' => '{unique_id}][answer][{method}][{url}][{body}][{internal_text}',

    /*
    |--------------------------------------------------------------------------
    | Error handling Configuration
    |--------------------------------------------------------------------------
    |
    | NOTE: Handler must implement \Grizmar\Api\Handlers\HandlerInterface
    */
    'error_handler' => \Grizmar\Api\Handlers\ErrorHandler::class,
];
