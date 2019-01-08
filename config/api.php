<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Messages and collections
    |--------------------------------------------------------------------------
    |
    | NOTE: Keeper must implement \Elantha\Api\Messages\KeeperInterface
    | NOTE: Collections must implement \Elantha\Api\Messages\CollectionInterface
    | Convenient way is to extend \Elantha\Api\Messages\BaseCollection
    */

    'message_keeper' => \Elantha\Api\Messages\Keeper::class,

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
    | Error handling Configuration
    |--------------------------------------------------------------------------
    |
    | NOTE: Handler must implement \Elantha\Api\Handlers\HandlerInterface
    */
    'error_handler' => \Elantha\Api\Handlers\ErrorHandler::class,

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log settings for your api.
    | NOTE: Logger handler must implement \Elantha\Api\LogLoggerInterface
    */

    'log' => env('API_LOG', false),

    'logger_handler' => \Elantha\Api\Log\Logger::class,

    'request_format' => '[{unique_id}][request][{method}][{url}][{query}][{body}]',

    'answer_format' => '[{unique_id}][answer][{method}][{url}][{body}][{internal_text}]',
];
