<?php

namespace Grizmar\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
use Grizmar\Api\Response\ContentInterface;
use Grizmar\Api\Response\JsonResponse;
use Grizmar\Api\Log\LoggerInterface;
use Grizmar\Api\Log\Logger;
use Grizmar\Api\Log\AccessLogger;
use Grizmar\Api\Messages\KeeperInterface;
use Grizmar\Api\Messages\Keeper;
use Illuminate\Support\Str;

class ApiServiceProvider extends ServiceProvider
{
    // TODO: вынести константы из провайдера
    public const CONTENT_TYPE_JSON = 'application/json';
    public const CONTENT_TYPE_XML = 'application/xml';

    /**
     * Bootstrap the application services.
     *
     * @param Request $request
     * @return void
     */
    public function boot(Request $request)
    {
        $this->publishes([
            __DIR__.'/../config/api.php' => config_path('api.php'),
        ]);

        $this->bindResponse($request);

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
        $responseClass = false;

        $types = (array) config('api.response_types', []);

        $contentType = $request->header('Content-type');

        if (!empty($contentType)) {
            foreach ($types as $type => $handler) {
                if (Str::is($type, $contentType)) {
                    $responseClass = $handler;
                    break;
                }
            }
        }

        if (!$responseClass) {
            $responseClass = $types['default'] ?? JsonResponse::class;
        }

        $this->app->bind(ContentInterface::class, $responseClass);
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

            if ($data instanceof ContentInterface) {
                $response = $data;
            }
            else {
                $response = resolve(ContentInterface::class);
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
