<?php

namespace Elantha\Api\Messages;


class Keeper implements KeeperInterface
{
    private $messages = [];

    public function load(BaseCollection $collection): KeeperInterface
    {
        $collection->init();

        $this->messages = $collection->getMessages() + $this->messages;

        return $this;
    }

    public function getMessage($code, array $context = [])
    {
        $result = '';

        $message = array_get($this->messages, $code);

        if (empty($message)) {
            $message = array_get($this->messages, 'default');
        }

        if ($message instanceof Message) {
            $result = $message->getText($context);
        }

        return $result;
    }
}
