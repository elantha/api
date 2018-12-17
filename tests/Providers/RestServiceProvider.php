<?php

namespace Grizmar\Api\Tests\Providers;

use Illuminate\Support\ServiceProvider;
use Grizmar\Api\Messages\KeeperInterface;
use Grizmar\Api\Tests\Errors\ErrorCollection;

/**
 * Class RestServiceProvider
 * Service provider example
 * @package Grizmar\Api\Tests\Providers
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
