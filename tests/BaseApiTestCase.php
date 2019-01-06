<?php

namespace Elantha\Api\Tests;

use Illuminate\Support\Str;
use Orchestra\Testbench\TestCase;

class BaseApiTestCase extends TestCase
{
    /** @var \Illuminate\Foundation\Application */
    protected $app;

    protected function getPackageProviders($app): array
    {
        return [
            \Elantha\Api\Providers\ApiServiceProvider::class,
            Providers\RestServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app->singleton(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            Exceptions\Handler::class
        );
    }

    public function setUp()
    {
        parent::setUp();

        $this->setUpRoutes();
    }

    protected function setUpRoutes()
    {
        /** @var \Illuminate\Routing\Router $router */
        $router  = $this->app['router'];

        $class   = new \ReflectionClass(Controllers\TestController::class);
        $methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method)
        {
            if (Str::is('test*', $method->getName())) {
                $router->post('test/' . $method->getName(), $class->getName() . '@' . $method->getName());
            }
        }
    }
}
