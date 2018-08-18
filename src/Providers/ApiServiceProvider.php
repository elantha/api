<?php

namespace Grizmar\Api\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
use Grizmar\Api\Response\ResponseInterface;
use Grizmar\Api\Response\JsonResponse;
use Grizmar\Api\Log\LoggerInterface;
use Grizmar\Api\Log\Logger;
use Grizmar\Api\Log\AccessLogger;
use Grizmar\Api\Messages\KeeperInterface;
use Grizmar\Api\Messages\Keeper;
use Grizmar\Api\Dispatch\DispatcherInterface;
use Grizmar\Api\Dispatch\HttpDispatcher;
use Grizmar\Api\Handlers\ErrorHandler;
use Grizmar\Api\Handlers\HandlerInterface;
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

        $this->bindHandler();
        
        $this->bindDispatcher();

        $this->bindLogger();
        
        $this->bindMessageKeeper();

        $this->responseMacro();
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

    private function bindHandler()
    {
        $handlerName = config('api.error_handler', ErrorHandler::class);

        $this->app->bind(HandlerInterface::class, $handlerName);
    }

    private function bindDispatcher()
    {
        $handlerName = config('api.dispatcher');

        if (!$handlerName) {
            $handlerName = HttpDispatcher::class;
        }

        $this->app->bind(DispatcherInterface::class, $handlerName);
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

    private function responseMacro()
    {
        Response::macro('rest', function ($data, $status = false) {

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

            resolve(LoggerInterface::class)->answer($response);

            return $response->getAnswer();
        });
    }
}
