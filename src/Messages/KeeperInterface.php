<?php

namespace Elantha\Api\Messages;


interface KeeperInterface
{
    public function load(BaseCollection $collection): self;

    public function getMessage($code, array $context = []);
}
