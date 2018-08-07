<?php

namespace Grizmar\Api\Messages;


interface KeeperInterface
{
    public function load(BaseCollection $collection): self;

    public function getMessage($code, array $context = []);
}
