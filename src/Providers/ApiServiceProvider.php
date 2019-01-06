<?php

namespace Elantha\Api\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
use Elantha\Api\Response\ResponseInterface;
use Elantha\Api\Response\JsonResponse;
use Elantha\Api\Log\LoggerInterface;
use Elantha\Api\Log\Logger;
use Elantha\Api\Log\AccessLogger;
use Elantha\Api\Messages\KeeperInterface;
use Elantha\Api\Messages\Keeper;
use Elantha\Api\Handlers\ErrorHandler;
use Elantha\Api\Handlers\HandlerInterface;
use Illuminate\Support\Str;

class ApiServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @param Request $request
     * @return void
     */
    public function boot(Request $request)
    {
        $this->publishes([
            __DIR__.'/../../config/api.php' => config_path('api.php'),
        ]);

        $this->bindResponse($request);

        $this->bindErrorHandler();

        $this->bindLogger();
        
        $this->bindMessageKeeper();

        $this->registerResponseMacro();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
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

    private function bindErrorHandler()
    {
        $handlerName = config('api.error_handler', ErrorHandler::class);

        $this->app->bind(HandlerInterface::class, $handlerName);
    }

    private function bindLogger()
    {
        $this->app->singleton(LoggerInterface::class, function ($app) {

            $handler = config('api.logger_handler', AccessLogger::class);

            return new Logger(new $handler('api'));
        });
    }

    private function bindMessageKeeper()
    {
        $this->app->singleton(KeeperInterface::class, Keeper::class);
    }

    private function registerResponseMacro()
    {
        Response::macro('rest', function ($data, $status = false) {

            /* @var ResponseInterface $response */
            if ($data instanceof ResponseInterface) {
                $response = $data;
            }
            else {
                $response = resolve(ResponseInterface::class);
                $response->setData($data);
            }

            if ($status) {
                $response->setStatusCode($status);
            }

            /* @var LoggerInterface $logger */
            $logger = resolve(LoggerInterface::class);
            $logger->answer($response);

            return $response->getAnswer();
        });
    }
}
