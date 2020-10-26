<?php

declare(strict_types=1);

namespace HTMLPurifier;

use HTMLPurifier\Log\PHPErrorLogger;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Class Log
 *
 * @package HTMLPurifier
 */
class Log
{
    /**
     * The logger instance.
     *
     * @var ?LoggerInterface
     */
    protected static $logger;

    /**
     * Sets a logger.
     *
     * @param LoggerInterface $logger
     */
    public static function setLogger(LoggerInterface $logger): void
    {
        static::$logger = $logger;
    }

    /**
     * System is unusable.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public static function emergency(string $message, array $context = []): void
    {
        static::log(LogLevel::EMERGENCY, $message, $context);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public static function alert(string $message, array $context = []): void
    {
        static::log(LogLevel::ALERT, $message, $context);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public static function critical(string $message, array $context = []): void
    {
        static::log(LogLevel::CRITICAL, $message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public static function error(string $message, array $context = []): void
    {
        static::log(LogLevel::ERROR, $message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public static function warning(string $message, array $context = []): void
    {
        static::log(LogLevel::WARNING, $message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public static function notice(string $message, array $context = []): void
    {
        static::log(LogLevel::NOTICE, $message, $context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public static function info(string $message, array $context = []): void
    {
        static::log(LogLevel::INFO, $message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public static function debug(string $message, array $context = []): void
    {
        static::log(LogLevel::DEBUG, $message, $context);
    }

    /**
     * @param mixed $level
     * @param mixed $message
     * @param array $context
     */
    public static function log($level, $message, array $context = []): void
    {
        if (static::$logger === null) {
            static::$logger = new PHPErrorLogger();
        }

        static::$logger->log($level, $message, $context);
    }
}
