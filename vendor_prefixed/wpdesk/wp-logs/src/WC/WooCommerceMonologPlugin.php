<?php

namespace BmWoocommerceVendor\WPDesk\Logger\WC;

use BmWoocommerceVendor\Monolog\Logger;
use Psr\Log\LogLevel;
use WC_Log_Levels;
/**
 * Can decorate monolog with WC_Logger_Interface
 *
 * @package WPDesk\Logger
 */
class WooCommerceMonologPlugin implements \WC_Logger_Interface
{
    /** @var Logger */
    private $monolog;
    /** @var \WC_Logger */
    private $originalWCLogger;
    public function __construct(\BmWoocommerceVendor\Monolog\Logger $monolog, \WC_Logger_Interface $originalLogger)
    {
        $this->monolog = $monolog;
        $this->originalWCLogger = $originalLogger;
    }
    /**
     * Method added for compatibility with \WC_Logger
     *
     * @param string $source
     */
    public function clear($source = '')
    {
        $this->originalWCLogger->clear($source);
    }
    /**
     * Method added for compatibility with \WC_Logger
     */
    public function clear_expired_logs()
    {
        $this->originalWCLogger->clear_expired_logs();
    }
    /**
     * Method for compatibility reason. Do not use.
     *
     * @param string $handle
     * @param string $message
     * @param string $level
     * @return bool|void
     *
     * @deprecated
     */
    public function add($handle, $message, $level = \WC_Log_Levels::NOTICE)
    {
        $this->log($message, $level);
    }
    /**
     * System is unusable.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function emergency($message, $context = array())
    {
        $this->log(\Psr\Log\LogLevel::EMERGENCY, $message, $context);
    }
    public function log($level, $message, $context = [])
    {
        $this->monolog->log($level, $message, $context);
    }
    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function alert($message, $context = array())
    {
        $this->log(\Psr\Log\LogLevel::ALERT, $message, $context);
    }
    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function critical($message, $context = array())
    {
        $this->log(\Psr\Log\LogLevel::CRITICAL, $message, $context);
    }
    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function error($message, $context = array())
    {
        $this->log(\Psr\Log\LogLevel::ERROR, $message, $context);
    }
    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function warning($message, $context = array())
    {
        $this->log(\Psr\Log\LogLevel::WARNING, $message, $context);
    }
    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function notice($message, $context = array())
    {
        $this->log(\Psr\Log\LogLevel::NOTICE, $message, $context);
    }
    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function info($message, $context = array())
    {
        $this->log(\Psr\Log\LogLevel::INFO, $message, $context);
    }
    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function debug($message, $context = array())
    {
        $this->log(\Psr\Log\LogLevel::DEBUG, $message, $context);
    }
}
