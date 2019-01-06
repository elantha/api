<?php

namespace Elantha\Api\Messages;


interface CollectionInterface
{
    public function init();

    public function getMessages(): array;
}
