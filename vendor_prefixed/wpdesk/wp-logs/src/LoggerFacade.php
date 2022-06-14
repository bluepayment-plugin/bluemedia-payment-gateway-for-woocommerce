<?php

namespace BmWoocommerceVendor\WPDesk\Logger;

use BmWoocommerceVendor\Monolog\Logger;
use Psr\Log\LogLevel;
use WP_Error;
use Exception;
/**
 * Facilitates creation of logger with default WPDesk settings
 *
 * @deprecated Only for backward compatibility. Please use injected Logger compatible with PSR
 *
 * @package WPDesk\Logger
 */
class LoggerFacade
{
    const BACKTRACE_FILENAME_KEY = 'file';
    /** @var WPDeskLoggerFactory */
    private static $factory;
    /**
     * Get logger by name. If not exists create one.
     *
     * @param string $name Name of the logger
     * @return Logger
     */
    public static function getLogger($name = \BmWoocommerceVendor\WPDesk\Logger\WPDeskLoggerFactory::DEFAULT_LOGGER_CHANNEL_NAME)
    {
        if (self::$factory === null) {
            self::$factory = new \BmWoocommerceVendor\WPDesk\Logger\WPDeskLoggerFactory();
        }
        return self::$factory->createWPDeskLogger($name);
    }
    /**
     * Snake case alias for getLogger
     *
     * @param string $name
     *
     * @return Logger
     */
    public static function get_logger($name = \BmWoocommerceVendor\WPDesk\Logger\WPDeskLoggerFactory::DEFAULT_LOGGER_CHANNEL_NAME)
    {
        return self::getLogger($name);
    }
    /**
     * If set, logs are disabled
     *
     * @param string $name Name of the logger
     */
    public static function set_disable_log($name = \BmWoocommerceVendor\WPDesk\Logger\WPDeskLoggerFactory::DEFAULT_LOGGER_CHANNEL_NAME)
    {
        self::$factory->disableLog($name);
    }
    /**
     * Log this exception into WPDesk logger
     *
     * @param WP_Error $e Error to log.
     * @param array $backtrace Backtrace information with snapshot of error env.
     * @param array $context Context to log
     * @param string $level Level of error.
     *
     * @see http://php.net/manual/en/function.debug-backtrace.php
     */
    public static function log_wp_error(\WP_Error $e, array $backtrace, array $context = array(), $level = \Psr\Log\LogLevel::ERROR)
    {
        $message = 'Error: ' . \get_class($e) . ' Code: ' . $e->get_error_code() . ' Message: ' . $e->get_error_message();
        self::log_message_backtrace($message, $backtrace, $context, $level);
    }
    /**
     * Log this exception into WPDesk logger
     *
     * @param Exception $e Exception to log.
     * @param array $context Context to log
     * @param string $level Level of error.
     */
    public static function log_exception(\Exception $e, array $context = array(), $level = \Psr\Log\LogLevel::ERROR)
    {
        $message = 'Exception: ' . \get_class($e) . ' Code: ' . $e->getCode() . ' Message: ' . $e->getMessage() . ' Stack: ' . $e->getTraceAsString();
        self::log_message($message, \array_merge($context, ['exception' => $e]), $e->getFile(), $level);
    }
    /**
     * Log message into WPDesk logger
     *
     * @param string $message Message to log.
     * @param array $context Context to log
     * @param string $source Source of the message - can be file name, class name or whatever.
     * @param string $level Level of error.
     */
    public static function log_message($message, array $context = array(), $source = null, $level = \Psr\Log\LogLevel::DEBUG)
    {
        $logger = self::getLogger();
        if ($source !== null) {
            $context = \array_merge($context, ['source' => $source]);
        }
        $logger->log($level, $message, $context);
    }
    /**
     * Log message into WPDesk logger
     *
     * @param string $message Message to log.
     * @param array $backtrace Backtrace information with snapshot of error env.
     * @param array $context Context to log
     * @param string $level Level of error.
     */
    public static function log_message_backtrace($message, array $backtrace, array $context = array(), $level = \Psr\Log\LogLevel::DEBUG)
    {
        $message .= ' Backtrace: ' . \json_encode($backtrace);
        $source = null;
        if (isset($backtrace[self::BACKTRACE_FILENAME_KEY])) {
            $source = $backtrace[self::BACKTRACE_FILENAME_KEY];
        }
        self::log_message($message, $context, $source, $level);
    }
}
