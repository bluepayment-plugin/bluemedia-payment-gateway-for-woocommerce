<?php

namespace BmWoocommerceVendor\WPDesk\Logger\WC;

use BmWoocommerceVendor\Monolog\Logger;
use BmWoocommerceVendor\WPDesk\Logger\WC\Exception\WCLoggerAlreadyCaptured;
/**
 * Can capture default WooCommerce logger
 *
 * @package WPDesk\Logger
 */
class WooCommerceCapture
{
    const WOOCOMMERCE_LOGGER_FILTER = 'woocommerce_logging_class';
    const WOOCOMMERCE_AFTER_IS_LOADED_ACTION = 'woocommerce_loaded';
    /** @var string Minimal version of WooCommerce supported by logger capture */
    const SUPPORTED_WC_VERSION = '3.5.0';
    /**
     * Is logger filter captured by library.
     *
     * @var bool
     */
    private $isCaptured = \false;
    /**
     * Our monolog
     *
     * @var Logger
     */
    private $monolog;
    /**
     * Original WC Logger
     *
     * @var \WC_Logger_Interface
     */
    private $originalWCLogger;
    /**
     * WordPress hook function to return our logger
     *
     * @var ?callable
     */
    private $captureHookFunction;
    /**
     * WordPress hook function to return original wc logger
     *
     * @var ?callable
     */
    private $freeHookFunction;
    public function __construct(\BmWoocommerceVendor\Monolog\Logger $monolog)
    {
        $this->monolog = $monolog;
    }
    /**
     * Prepares callable property captureHookFunction.
     * For it to work WC have to be loaded
     */
    private function prepareCaptureHookCallable()
    {
        $monolog = $this->monolog;
        if ($this->captureHookFunction === null) {
            $this->captureHookFunction = function () use($monolog) {
                return new \BmWoocommerceVendor\WPDesk\Logger\WC\WooCommerceMonologPlugin($monolog, $this->originalWCLogger);
            };
            $this->monolog->pushHandler(new \BmWoocommerceVendor\WPDesk\Logger\WC\WooCommerceHandler($this->originalWCLogger));
        }
    }
    /**
     * Is this version of WooCommerce is supported by logger capture
     *
     * @return bool
     */
    public static function isSupportedWCVersion()
    {
        return \class_exists(\WooCommerce::class) && \version_compare(\WooCommerce::instance()->version, self::SUPPORTED_WC_VERSION, '>=');
    }
    /**
     * Capture WooCommerce logger and inject our decorated Logger
     */
    public function captureWcLogger()
    {
        if (self::isSupportedWCVersion()) {
            if ($this->isCaptured) {
                throw new \BmWoocommerceVendor\WPDesk\Logger\WC\Exception\WCLoggerAlreadyCaptured('Try to free wc logger first.');
            }
            if ($this->isWooCommerceLoggerAvailable()) {
                $this->prepareFreeHookCallable();
                $this->prepareCaptureHookCallable();
                \remove_filter(self::WOOCOMMERCE_LOGGER_FILTER, $this->freeHookFunction);
                \add_filter(self::WOOCOMMERCE_LOGGER_FILTER, $this->captureHookFunction);
                $this->isCaptured = \true;
            } elseif (\function_exists('add_action')) {
                \add_action(self::WOOCOMMERCE_AFTER_IS_LOADED_ACTION, [$this, 'captureWcLogger']);
            } else {
                $this->monolog->alert('Cannot capture WC - WordPress is not available.');
            }
        } else {
            $this->monolog->alert('Cannot capture WC - WooCommerce version is not supported.');
        }
    }
    /**
     * Can i fetch WC Logger?
     *
     * @return bool
     */
    private function isWooCommerceLoggerAvailable()
    {
        return \function_exists('wc_get_logger');
    }
    /**
     * Prepares callable property freeHookFunction.
     * For it to work WC have to be loaded
     */
    private function prepareFreeHookCallable()
    {
        if ($this->freeHookFunction === null) {
            $this->originalWCLogger = $logger = \wc_get_logger();
            $this->freeHookFunction = function () use($logger) {
                return $logger;
            };
        }
    }
    /**
     * Remove WooCommerce logger injection
     */
    public function freeWcLogger()
    {
        if ($this->isCaptured) {
            \remove_filter(self::WOOCOMMERCE_LOGGER_FILTER, $this->captureHookFunction);
            \add_filter(self::WOOCOMMERCE_LOGGER_FILTER, $this->freeHookFunction);
            $this->isCaptured = \false;
        }
    }
}
