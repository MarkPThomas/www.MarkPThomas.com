<?php

namespace markpthomas\library;

/**
 * Logging class. Saves or displays messages.
 */
class MyLogger {
    public static $echoToScreen = true;

    /**
     * @var string Log message to which additional log messages are appended.
     */
    public static $message = '';

    /**
     * Writes the message immediately to the console or screen., or logs provided message to the logger message property.
     * The action depends on the current value set for $echoToScreen.
     * @param string $message Message to log.
     */
    public static function log($message){
        if (self::$echoToScreen)
        {
            echo $message;
            ob_flush();
            flush();
        }
        else
        {
            self::$message .= $message;
        }
    }
}