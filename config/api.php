<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Messages and collections
    |--------------------------------------------------------------------------
    |
    | NOTE: Collections must implement \Elantha\Api\Messages\CollectionInterface
    | convenient way is to extend \Elantha\Api\Messages\BaseCollection
    */

    'message_collections' => [],

    /*
    |--------------------------------------------------------------------------
    | Response Configuration
    |--------------------------------------------------------------------------
    |
    | Response types as 'Content-Type' => Response type handler
    | NOTE: Response handler must implement \Elantha\Api\Response\ResponseInterface
    */

    'response_types' => [
        'application/xml' => \Elantha\Api\Response\XmlResponse::class,
        'default' => \Elantha\Api\Response\JsonResponse::class,
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

    'logger_handler' => \Elantha\Api\Log\AccessLogger::class,

    'request_format' => '{unique_id}][request][{method}][{url}][{body}',

    'answer_format' => '{unique_id}][answer][{method}][{url}][{body}][{internal_text}',

    /*
    |--------------------------------------------------------------------------
    | Error handling Configuration
    |--------------------------------------------------------------------------
    |
    | NOTE: Handler must implement \Elantha\Api\Handlers\HandlerInterface
    */
    'error_handler' => \Elantha\Api\Handlers\ErrorHandler::class,
];
