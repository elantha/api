<?php

namespace Elantha\Api\Tests\Providers;

use Illuminate\Support\ServiceProvider;
use Elantha\Api\Messages\KeeperInterface;
use Elantha\Api\Tests\Errors\ErrorCollection;

/**
 * Class RestServiceProvider
 * Service provider example
 * @package Elantha\Api\Tests\Providers
 */
class RestServiceProvider extends ServiceProvider
{
    public function boot(KeeperInterface $keeper)
    {
        $keeper->load(new ErrorCollection());
    }

    public function register()
    {
    }
}
