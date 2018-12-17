<?php

namespace Grizmar\Api\Tests\Errors;

use Grizmar\Api\Messages\BaseCollection;

/**
 * Class ErrorCollection
 * Error collection example
 * @package Grizmar\Api\Tests\Errors
 */
class ErrorCollection extends BaseCollection
{
    public function init()
    {
        $this->addMessages([
            'default'                    => 'Sorry, something went wrong!',
            CodeRegistry::USER_NOT_FOUND => 'User not found: :name',
        ]);
    }
}
