<?php

namespace Grizmar\Api\Messages;


class Keeper
{
    private static $baseCollection = [];
    private static $logCollection = [];

    public static function load(BaseCollection $collection, $isLog = false): void
    {
        $collection->init();

        $messages = $collection->getMessages();

        if ($isLog)
            self::$logCollection = $messages + self::$logCollection;
        else
            self::$baseCollection = $messages + self::$baseCollection;
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
