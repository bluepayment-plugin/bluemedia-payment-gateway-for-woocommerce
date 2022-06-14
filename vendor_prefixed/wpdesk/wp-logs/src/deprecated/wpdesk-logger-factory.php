<?php

namespace BmWoocommerceVendor;

if (!\defined('ABSPATH')) {
    exit;
}
if (!\class_exists('BmWoocommerceVendor\\WPDesk_Logger_Factory')) {
    /**
     * @deprecated Only for backward compatibility. Please use injected Logger compatible with PSR
     */
    class WPDesk_Logger_Factory
    {
        /**
         * Static logger storage
         *
         * @var WPDesk_Logger
         */
        private static $logger = null;
        const BACKTRACE_FILENAME_KEY = 'file';
        const WPDESK_LOG_ACTION_NAME = 'wpdesk_log';
        /**
         * Creates and returns a logger
         *
         * @return WPDesk_Logger
         */
        public static function create_logger()
        {
            if (empty(self::$logger)) {
                $logger = new \BmWoocommerceVendor\WPDesk_Logger();
                $logger->attach_hooks();
                self::$logger = $logger;
            }
            return self::$logger;
        }
        /**
         * Log this exception into wpdesk logger
         *
         * @param WP_Error $e Error to log.
         * @param array    $backtrace Backtrace information with snapshot of error env.
         *
         * @see http://php.net/manual/en/function.debug-backtrace.php
         */
        public static function log_wp_error(\WP_Error $e, array $backtrace)
        {
            $message = 'Error: ' . \get_class($e) . ' Code: ' . $e->get_error_code() . ' Message: ' . $e->get_error_message();
            self::log_message_backtrace($message, \BmWoocommerceVendor\WPDesk_Logger::ERROR, $backtrace);
        }
        /**
         * Log this exception into WPDesk logger
         *
         * @param Exception $e Exception to log.
         */
        public static function log_exception(\Exception $e)
        {
            $message = 'Exception: ' . \get_class($e) . ' Code: ' . $e->getCode() . ' Message: ' . $e->getMessage() . ' Stack: ' . $e->getTraceAsString();
            self::log_message($message, $e->getFile(), \BmWoocommerceVendor\WPDesk_Logger::ERROR);
        }
        /**
         * Log message into WPDesk logger
         *
         * @param string $message Message to log.
         * @param string $source Source of the message - can be file name, class name or whatever.
         * @param string $level Level of error.
         */
        public static function log_message($message, $source = 'unknown', $level = \BmWoocommerceVendor\WPDesk_Logger::DEBUG)
        {
            self::create_logger();
            \do_action(self::WPDESK_LOG_ACTION_NAME, $level, $source, $message);
            self::$logger->wpdesk_log($level, $source, $message);
        }
        /**
         * Log message into WPDesk logger
         *
         * @param string $message Message to log.
         * @param string $level Level of error.
         * @param array  $backtrace Backtrace information with snapshot of error env.
         */
        public static function log_message_backtrace($message, $level = \BmWoocommerceVendor\WPDesk_Logger::DEBUG, array $backtrace)
        {
            $message .= ' Backtrace: ' . \json_encode($backtrace);
            if (isset($backtrace[self::BACKTRACE_FILENAME_KEY])) {
                $filename = $backtrace[self::BACKTRACE_FILENAME_KEY];
            } else {
                $filename = 'unknown';
            }
            self::log_message($message, $filename, $level);
        }
    }
}
