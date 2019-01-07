<?php

namespace Elantha\Api\Messages;


interface CollectionInterface
{
    public function init(): void;

    public function getMessages(): array;
}
