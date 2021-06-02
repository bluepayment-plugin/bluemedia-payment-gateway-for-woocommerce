<?php

namespace BlueMedia\OnlinePayments\Util;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class Logger
{
    const EMERGENCY = LogLevel::EMERGENCY;
    const ALERT = LogLevel::ALERT;
    const CRITICAL = LogLevel::CRITICAL;
    const ERROR = LogLevel::ERROR;
    const WARNING = LogLevel::WARNING;
    const NOTICE = LogLevel::NOTICE;
    const INFO = LogLevel::INFO;
    const DEBUG = LogLevel::DEBUG;

    /** @var LoggerInterface */
    protected static $logger;

    /**
     * Sets a logger.
     *
     * @param LoggerInterface $logger
     *
     */
    public static function setLogger(LoggerInterface $logger)
    {
        self::$logger = $logger;
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     *
     * @return void
     *
     */
    public static function log($level, $message, array $context = [])
    {
        if (self::$logger instanceof LoggerInterface) {
            self::$logger->log($level, $message, $context);
        }
    }
}
