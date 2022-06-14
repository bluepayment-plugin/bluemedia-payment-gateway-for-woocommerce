<?php

namespace BmWoocommerceVendor\WPDesk\Plugin\Flow\Initialization\Simple;

use BmWoocommerceVendor\WPDesk\Helper\PrefixedHelperAsLibrary;
/**
 * Trait helps with helper initialization
 *
 * @package WPDesk\Plugin\Flow\Initialization\Simple
 */
trait HelperInstanceAsFilterTrait
{
    /** @var \WPDesk\Helper\PrefixedHelperAsLibrary */
    private static $helper_instance;
    /**
     * Returns version of the helper. Inc when helper is changed and should be instantiated fist.
     *
     * @return int
     */
    private function get_helper_version()
    {
        return 5;
    }
    /**
     * Returns filter action name for helper instance
     *
     * @return string
     */
    private function get_helper_action_name()
    {
        return 'wpdesk_helper_instance';
    }
    /**
     * Instantiate helper and return it
     *
     * @return PrefixedHelperAsLibrary
     */
    private function get_helper_instance()
    {
        return \apply_filters($this->get_helper_action_name(), null);
    }
    /**
     * Prepare helper to be instantiated using wpdesk_helper_instance filter
     *
     * @return void|PrefixedHelperAsLibrary
     */
    private function prepare_helper_action()
    {
        \class_exists(\WPDesk\Helper\HelperAsLibrary::class);
        // autoload this class
        \add_filter($this->get_helper_action_name(), function ($helper_instance) {
            if (\is_object($helper_instance)) {
                return $helper_instance;
            }
            if (\is_object(self::$helper_instance)) {
                return self::$helper_instance;
            }
            if (\apply_filters('wpdesk_can_start_helper', \true, $this->plugin_info)) {
                self::$helper_instance = new \BmWoocommerceVendor\WPDesk\Helper\PrefixedHelperAsLibrary();
                self::$helper_instance->hooks();
                \do_action('wpdesk_helper_started', self::$helper_instance, $this->plugin_info);
                return self::$helper_instance;
            }
        }, 10 - $this->get_helper_version());
    }
}
