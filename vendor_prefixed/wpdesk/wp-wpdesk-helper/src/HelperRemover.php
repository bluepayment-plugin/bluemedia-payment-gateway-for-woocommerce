<?php

namespace BmWoocommerceVendor\WPDesk\Helper;

use WP_Hook;
use BmWoocommerceVendor\WPDesk\Helper\Integration\LicenseIntegration;
use BmWoocommerceVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use BmWoocommerceVendor\WPDesk\PluginBuilder\Storage\Exception\ClassNotExists;
use BmWoocommerceVendor\WPDesk\PluginBuilder\Storage\StorageFactory;
/**
 * Tries to remove all traces of helper
 *
 * @package WPDesk\Helper
 */
class HelperRemover implements \BmWoocommerceVendor\WPDesk\PluginBuilder\Plugin\Hookable
{
    const DEFAULT_FILTER_PRIORITY = 10;
    const MAIN_WPDESK_MENU_POSITION = 99.99941337;
    const CALLBACK_KEY = 'function';
    const CALLBACK_KEY_WITH_FUNCTION_NAME = 1;
    const REMOVE_WPDESK_MENU_PRIORITY = 11;
    /**
     * Prevents attaching hooks more than once when used in many plugins
     *
     * @var bool
     */
    private static $already_hooked;
    public function hooks()
    {
        if (!self::$already_hooked && \is_admin()) {
            self::$already_hooked = \true;
            $this->hide_notice_about_helper_need();
            $this->remove_helper_updates();
            $this->remove_wpdesk_menu();
            $this->remove_message_about_log();
        }
    }
    /**
     * Hides notice from plugin that helper is needed
     *
     * @return void
     */
    private function hide_notice_about_helper_need()
    {
        \remove_action('admin_notices', 'wpdesk_helper_notice');
        $this->remove_object_action_by_name('admin_notices', 10, 'wpdesk_helper_notice');
    }
    /**
     * Removes action/filter using object method callbacks name. You don't need an object instance to use this.
     *
     * @param string $action_name
     * @param int    $priority
     * @param string $function_name
     */
    private function remove_object_action_by_name($action_name, $priority, $function_name)
    {
        global $wp_filter;
        if (isset($wp_filter[$action_name]) && isset($wp_filter[$action_name]->callbacks[$priority])) {
            /** @var WP_Hook $admin_notices_tag */
            $admin_notices_tag = $wp_filter[$action_name];
            $default_priority_callbacks = $admin_notices_tag->callbacks[$priority];
            foreach ($default_priority_callbacks as $callback) {
                if (\is_array($callback) && \is_array($callback[self::CALLBACK_KEY]) && isset($callback[self::CALLBACK_KEY][self::CALLBACK_KEY_WITH_FUNCTION_NAME])) {
                    $found_function_name = $callback[self::CALLBACK_KEY][self::CALLBACK_KEY_WITH_FUNCTION_NAME];
                    if ($found_function_name === $function_name) {
                        $admin_notices_tag->remove_filter($action_name, $callback[self::CALLBACK_KEY], $priority);
                    }
                }
            }
        }
    }
    /**
     * Removes wpdesk helper menu if was added by helper.
     *
     * @return void
     */
    private function remove_wpdesk_menu()
    {
        \add_action('admin_menu', function () {
            $this->handle_remove_wpdesk_menu();
        }, self::REMOVE_WPDESK_MENU_PRIORITY);
    }
    /**
     * Removes wpdesk helper update routines if was added by helper.
     *
     * @return void
     */
    private function remove_helper_updates()
    {
        // have to be in plugins_loaded because helper is loaded simultaneously with this plugin
        \add_action('plugins_loaded', function () {
            \remove_action('plugins_loaded', [$this->get_helper_instance(), 'init_helper_plugins'], \BmWoocommerceVendor\WPDesk\Helper\Integration\LicenseIntegration::PRIORITY_HELPER_UPDATE);
        });
    }
    /**
     * @return void
     */
    private function handle_remove_wpdesk_menu()
    {
        $wpdesk_helper = $this->get_helper_instance();
        if ($wpdesk_helper) {
            \remove_submenu_page('wpdesk-helper', 'wpdesk-licenses');
            \remove_action('wp-desk_page_wpdesk-licenses', [$wpdesk_helper, 'wpdesk_licenses']);
            \remove_submenu_page('wpdesk-helper', 'wpdesk-helper-settings');
            \remove_action('wp-desk_page_wpdesk-helper-settings', [$wpdesk_helper, 'wpdesk_helper_settings']);
            \remove_menu_page('wpdesk-helper');
        }
    }
    /**
     * @return void
     */
    private function remove_message_about_log()
    {
        \add_filter('wpdesk_helper_show_log_notices', '__return_false');
    }
    /**
     * Fetches WPDesk_Helper instance if it's available.
     *
     * @return \WPDesk_Helper|null
     */
    private function get_helper_instance()
    {
        $storage = new \BmWoocommerceVendor\WPDesk\PluginBuilder\Storage\StorageFactory();
        try {
            return $storage->create_storage()->get_from_storage('WPDesk_Helper');
        } catch (\BmWoocommerceVendor\WPDesk\PluginBuilder\Storage\Exception\ClassNotExists $e) {
            return null;
        }
    }
}
