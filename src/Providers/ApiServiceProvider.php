<?php

namespace Elantha\Api\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Elantha\Api\Response\ResponseInterface;
use Elantha\Api\Response\JsonResponse;
use Elantha\Api\Log\LoggerInterface;
use Elantha\Api\Log\Logger;
use Elantha\Api\Messages\CollectionInterface;
use Elantha\Api\Messages\KeeperInterface;
use Elantha\Api\Messages\Keeper;
use Elantha\Api\Handlers\ErrorHandler;
use Elantha\Api\Handlers\HandlerInterface;

class ApiServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @param Request $request
     * @return void
     */
    public function boot(Request $request): void
    {
        $this->publishes([
            __DIR__.'/../../config/api.php' => config_path('api.php'),
        ]);

        $this->bindResponse($request);

        $this->bindErrorHandler();

        $this->bindLogger();
        
        $this->bindMessageKeeper();

        $this->registerResponseMacro();

        $this->initMessageCollections();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register(): void
    {

    }

    private function bindResponse(Request $request): void
    {
        $handlerName = false;

        $types = (array) config('api.response_types', []);

        $contentType = $request->header('Content-type');

        if (!empty($contentType)) {
            foreach ($types as $type => $handler) {
                if (Str::is($type, $contentType)) {
                    $handlerName = $handler;
                    break;
                }
            }
        }

        if (!$handlerName) {
            $handlerName = $types['default'] ?? JsonResponse::class;
        }

        $this->app->bind(ResponseInterface::class, $handlerName);
    }

    private function bindErrorHandler(): void
    {
        $handlerName = config('api.error_handler', ErrorHandler::class);

        $this->app->bind(HandlerInterface::class, $handlerName);
    }

    private function bindLogger(): void
    {
        $this->app->singleton(LoggerInterface::class, function ($app) {

            $logger = config('api.logger_handler', Logger::class);

            return new $logger();
        });
    }

    private function bindMessageKeeper(): void
    {
        $this->app->singleton(KeeperInterface::class, function ($app) {

            $keeper = config('api.message_keeper', Keeper::class);

            return new $keeper();
        });
    }

    private function registerResponseMacro(): void
    {
        Response::macro('rest', function ($data, $status = false) {

            /* @var ResponseInterface $response */
            if ($data instanceof ResponseInterface) {
                $response = $data;
            } else {
                $response = resolve(ResponseInterface::class);
                $response->setData($data);
            }

            if ($status) {
                $response->setStatusCode($status);
            }

            /* @var LoggerInterface $logger */
            $logger = resolve(LoggerInterface::class);
            $logger->logAnswer($response);

            return $response->getAnswer();
        });
    }

    private function initMessageCollections(): void
    {
        $messageCollections = (array) config('api.message_collections', []);

        /** @var KeeperInterface $keeper */
        $keeper = resolve(KeeperInterface::class);

        foreach ($messageCollections as $className) {

            if (is_subclass_of($className, CollectionInterface::class)) {
                $keeper->load(new $className());
            }
        }
    }
}
