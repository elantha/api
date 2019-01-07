<?php

namespace Elantha\Api\Tests\Errors;

use Elantha\Api\Messages\BaseCollection;

/**
 * Class ErrorCollection
 * Error collection example
 * @package Elantha\Api\Tests\Errors
 */
class ErrorCollection extends BaseCollection
{
    public function init(): void
    {
        $this->addMessages([
            'default'                    => 'Sorry, something went wrong!',
            CodeRegistry::USER_NOT_FOUND => 'User not found: :name',
        ]);
    }
}
