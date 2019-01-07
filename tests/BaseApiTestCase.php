<?php

namespace Elantha\Api\Tests;

use Orchestra\Testbench\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class BaseApiTestCase extends TestCase
{
    /** @var \Illuminate\Foundation\Application */
    protected $app;

    protected function getPackageProviders($app): array
    {
        return [
            \Elantha\Api\Providers\ApiServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        /** @var \Illuminate\Config\Repository $config */
        $config = $app['config'];

        $config->set('api.message_collections', [
            Errors\ErrorCollection::class,
        ]);

        $app->singleton(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            Exceptions\Handler::class
        );
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->setUpRoutes();
    }

    protected function setUpRoutes(): void
    {
        /** @var \Illuminate\Routing\Router $router */
        $router = $this->app['router'];

        $class = new \ReflectionClass(Controllers\TestController::class);
        $methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            if (Str::is('test*', $method->getName())) {
                $router->post('test/' . $method->getName(), $class->getName() . '@' . $method->getName());
            }
        }
    }

    protected function invokeRequest(string $sourceUrl, array $params = []): Response
    {
        $request = Request::create('test/' . $sourceUrl, 'POST', $params);

        return $this->app->handle($request);
    }
}
