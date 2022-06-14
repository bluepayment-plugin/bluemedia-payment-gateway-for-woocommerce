<?php

namespace BmWoocommerceVendor\WPDesk\Logger;

use Exception;
use InvalidArgumentException;
use LogicException;
use BmWoocommerceVendor\Monolog\Handler\NullHandler;
use BmWoocommerceVendor\Monolog\Logger;
use BmWoocommerceVendor\Monolog\Registry;
use BmWoocommerceVendor\Monolog\Handler\StreamHandler;
use Psr\Log\LogLevel;
use BmWoocommerceVendor\WPDesk\Logger\WC\WooCommerceCapture;
use BmWoocommerceVendor\WPDesk\Logger\WP\WPCapture;
/**
 * Manages and facilitates creation of logger
 *
 * @package WPDesk\Logger
 */
class WPDeskLoggerFactory extends \BmWoocommerceVendor\WPDesk\Logger\BasicLoggerFactory
{
    const DEFAULT_LOGGER_CHANNEL_NAME = 'wpdesk';
    /** @var string Log to file when level is */
    const LEVEL_WPDESK_FILE = \Psr\Log\LogLevel::DEBUG;
    /** @var string Log to wc logger when level is */
    const LEVEL_WC = \Psr\Log\LogLevel::ERROR;
    /** @var bool Will factory return null logger or not */
    public static $shouldLoggerBeActivated = \true;
    /**
     * Remove static instances. In general should be use only testing purposes.
     *
     * @param string $name Name of the logger
     */
    public static function tearDown($name = self::DEFAULT_LOGGER_CHANNEL_NAME)
    {
        if (\BmWoocommerceVendor\Monolog\Registry::hasLogger($name)) {
            \BmWoocommerceVendor\Monolog\Registry::removeLogger($name);
        }
    }
    /**
     * Disable logger. Still exists but logs won't be saved
     *
     * @param string $name Name of the logger
     */
    public function disableLog($name = self::DEFAULT_LOGGER_CHANNEL_NAME)
    {
        if (!\BmWoocommerceVendor\Monolog\Registry::hasLogger($name)) {
            $this->createWPDeskLogger($name);
        }
        if (\BmWoocommerceVendor\Monolog\Registry::hasLogger($name)) {
            /** @var Logger $logger */
            $logger = \BmWoocommerceVendor\Monolog\Registry::getInstance($name);
            $this->removeAllHandlers($logger);
        }
    }
    /**
     * Creates default WPDesk logger.
     *
     * Requirements:
     * - get_option, add/remove_action, add/remove filter and WP_CONTENT_DIR should be available for logger.
     *
     * Assumptions:
     * - logger is actively working when 'wpdesk_helper_options' has 'debug_log' set to '1';
     * - fatal errors, exception and standard errors are recorded but in a transparent way;
     * - WooCommerce logger is captured and returns this logger; That is true of default logger is instantiated.
     * - logs are still correctly written to WooCommerce subsystem in a transparent way;
     * - all recorded errors are written to WPDesk file.
     *
     * @param string $name Name of the logger
     * @return Logger
     */
    public function createWPDeskLogger($name = self::DEFAULT_LOGGER_CHANNEL_NAME)
    {
        if (!self::$shouldLoggerBeActivated) {
            return new \BmWoocommerceVendor\Monolog\Logger($name);
        }
        if (\BmWoocommerceVendor\Monolog\Registry::hasLogger($name)) {
            return \BmWoocommerceVendor\Monolog\Registry::getInstance($name);
        }
        $logger = $this->createLogger($name);
        if (self::isWPLogPermitted()) {
            $this->appendMainLog($logger);
        }
        if ($name !== self::DEFAULT_LOGGER_CHANNEL_NAME) {
            $this->appendFileLog($logger, $this->getFileName($name));
        } else {
            $this->captureWooCommerce($logger);
        }
        return $logger;
    }
    /**
     * Is capturing the php log is permitted.
     *
     * @return bool
     */
    public static function isWPLogPermitted()
    {
        return \apply_filters('wpdesk_is_wp_log_capture_permitted', \true);
    }
    /**
     * @param $logger
     */
    private function appendMainLog($logger)
    {
        $wpCapture = $this->captureWPLog();
        if (\is_writable($wpCapture->get_log_file())) {
            $this->appendFileLog($logger, $wpCapture->get_log_file());
        }
    }
    /**
     * @param Logger $logger
     */
    private function appendFileLog($logger, $filename)
    {
        try {
            $this->pushFileHandle($filename, $logger);
        } catch (\InvalidArgumentException $e) {
            $logger->emergency('Main log file could not be created - invalid filename.');
        } catch (\Exception $e) {
            $logger->emergency('Main log file could not be written.');
        }
    }
    /**
     * @return WPCapture
     */
    private function captureWPLog()
    {
        static $wpCapture;
        if (!$wpCapture) {
            $wpCapture = new \BmWoocommerceVendor\WPDesk\Logger\WP\WPCapture(\basename($this->getFileName()));
            $wpCapture->init_debug_log_file();
        }
        return $wpCapture;
    }
    /**
     * Capture WooCommerce and add handle
     *
     * @param Logger $logger
     */
    private function captureWooCommerce(\BmWoocommerceVendor\Monolog\Logger $logger)
    {
        if (!\defined('BmWoocommerceVendor\\WC_LOG_THRESHOLD')) {
            \define('BmWoocommerceVendor\\WC_LOG_THRESHOLD', self::LEVEL_WC);
        }
        $wcIntegration = new \BmWoocommerceVendor\WPDesk\Logger\WC\WooCommerceCapture($logger);
        $wcIntegration->captureWcLogger();
    }
    /**
     * Add WPDesk log file handle
     *
     * @param Logger $logger
     * @param string $filename Name of file with path
     *
     * @throws Exception                If a missing directory is not buildable
     * @throws InvalidArgumentException If stream is not a resource or string
     */
    private function pushFileHandle($filename, \BmWoocommerceVendor\Monolog\Logger $logger)
    {
        $logger->pushHandler(new \BmWoocommerceVendor\Monolog\Handler\StreamHandler($filename, self::LEVEL_WPDESK_FILE));
    }
    /**
     * Get filename old way
     *
     * @deprecated not sure if can remove
     */
    public function getWPDeskFileName()
    {
        return $this->getFileName();
    }
    /**
     * Returns WPDesk filename.
     *
     * @param string $name Name of the logger
     *
     * @return string
     */
    public function getFileName($name = self::DEFAULT_LOGGER_CHANNEL_NAME)
    {
        $upload_dir = \wp_upload_dir();
        return \trailingslashit(\untrailingslashit($upload_dir['basedir'])) . \BmWoocommerceVendor\WPDesk\Logger\WP\WPCapture::LOG_DIR . \DIRECTORY_SEPARATOR . $name . '_debug.log';
    }
    /**
     * Removes all handlers from logger
     *
     * @param Logger $logger
     *
     * @return void
     */
    private function removeAllHandlers(\BmWoocommerceVendor\Monolog\Logger $logger)
    {
        try {
            while (\true) {
                $logger->popHandler();
            }
        } catch (\LogicException $e) {
            $logger->pushHandler(new \BmWoocommerceVendor\Monolog\Handler\NullHandler());
        }
    }
    /**
     * is WPDesk file log is working(writable, exists, connected).
     * @param string $name Name of the logger
     *
     * @return bool
     */
    public function isLogWorking($name = self::DEFAULT_LOGGER_CHANNEL_NAME)
    {
        return \BmWoocommerceVendor\Monolog\Registry::hasLogger($name);
    }
}
