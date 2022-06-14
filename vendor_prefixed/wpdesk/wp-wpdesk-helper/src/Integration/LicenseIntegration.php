<?php

namespace BmWoocommerceVendor\WPDesk\Helper\Integration;

use BmWoocommerceVendor\WPDesk\License\Page\LicensePage;
use BmWoocommerceVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use BmWoocommerceVendor\WPDesk\License\InstalledPlugins;
/**
 * Integrates WP Desk licenses with WordPress
 *
 * @package WPDesk\Helper
 */
class LicenseIntegration implements \BmWoocommerceVendor\WPDesk\PluginBuilder\Plugin\Hookable
{
    /** @var LicensePage */
    private $license_page;
    /** @var InstalledPlugins */
    private $plugin_registry;
    const PRIORITY_HELPER_UPDATE = 9999999;
    const PRIORITY_AFTER_WPDESK_NEW_MENU_ADDED = 20;
    public function __construct()
    {
        $plugin_registry = new \BmWoocommerceVendor\WPDesk\License\InstalledPlugins();
        $this->license_page = new \BmWoocommerceVendor\WPDesk\License\Page\LicensePage($plugin_registry);
        $this->plugin_registry = $plugin_registry;
    }
    /**
     * @return void
     */
    public function hooks()
    {
        $this->license_page->hooks();
        $this->add_helper_updates();
        $this->add_license_page();
    }
    /**
     * @return void
     */
    private function add_helper_updates()
    {
        // have to be in plugins_loaded because helper is loaded simultaneously with this plugin
        \add_action('plugins_loaded', function () {
            \add_action('plugins_loaded', [$this->plugin_registry, 'refresh_plugin_update_info'], self::PRIORITY_HELPER_UPDATE);
        });
    }
    /**
     * Replace licenses page if helper exists or add that page if helper not exists.
     *
     * @return void
     */
    private function add_license_page()
    {
        \add_action('admin_menu', function () {
            $this->license_page->handle_add_page_submenu_item();
        }, self::PRIORITY_AFTER_WPDESK_NEW_MENU_ADDED);
    }
}
