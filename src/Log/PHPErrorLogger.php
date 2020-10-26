<?php

declare(strict_types=1);

namespace HTMLPurifier\Log;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

/**
 * Class PHPErrorLogger
 *
 * @package HTMLPurifier\Log
 */
class PHPErrorLogger extends AbstractLogger
{
    /**
     * @param mixed $level
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function log($level, $message, array $context = []): void
    {
        /** @psalm-suppress ArgumentTypeCoercion */
        trigger_error($message, $this->translateErrorLevel((string)$level));
    }

    /**
     * @param string $level
     * @return int
     */
    private function translateErrorLevel(string $level): int
    {
        switch ($level) {
            case LogLevel::EMERGENCY:
            case LogLevel::ALERT:
            case LogLevel::CRITICAL:
            case LogLevel::ERROR:
                return E_USER_ERROR;
            case LogLevel::WARNING:
                return E_USER_WARNING;
            case LogLevel::NOTICE:
            case LogLevel::INFO:
            case LogLevel::DEBUG:
            default:
                return E_USER_NOTICE;
        }
    }
}
