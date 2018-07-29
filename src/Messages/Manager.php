<?php

namespace Grizmar\Api\Messages;


class Manager
{
    private static $baseCollection = [];
    private static $logCollection = [];

    public static function load(BaseCollection $collection): void
    {
        self::boot(self::$baseCollection, $collection);
    }

    public static function loadLogCollection(BaseCollection $collection): void
    {
        self::boot(self::$logCollection, $collection);
    }

    public static function getMessage($code, array $context = []): string
    {
        return self::getDirectMessage(self::$baseCollection, $code, $context);
    }

    public static function getLogMessage($code, array $context = []): string
    {
        $message = self::getDirectMessage(self::$logCollection, $code, $context);

        if (empty($message)) {
            self::getMessage(self::$logCollection, $code, $context);
        }

        return $message;
    }

    private static function boot(array &$currentCollection, BaseCollection $collection): void
    {
        $collection->init();

        $currentCollection = $collection->getMessages() + $currentCollection;
    }

    private static function getDirectMessage($collection, $code, array $context = []): string
    {
        $result = '';

        $message = array_get($collection, $code);

        if (empty($message)) {
            $message = array_get($collection, 'default');
        }

        if ($message instanceof Message) {
            $result = $message->getText($context);
        }

        return $result;
    }
}
