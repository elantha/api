<?php

namespace Elantha\Api\Messages;


abstract class BaseCollection
{
    private $messages = [];

    abstract public function init();

    final public function addMessages(array $messages): self
    {
        foreach ($messages as $code => $message) {

            if ($message instanceof Message) {
                $this->pushMessage($message);
            } else {
                $this->addMessage($code, $message);
            }
        }

        return $this;
    }

    final public function addMessage($code, string $text): self
    {
        $this->pushMessage(new Message($code, $text));

        return $this;
    }

    final public function pushMessage(Message $message): self
    {
        array_set($this->messages, $message->getCode(), $message);

        return $this;
    }

    public function getMessages(): array
    {
        return $this->messages;
    }
}
