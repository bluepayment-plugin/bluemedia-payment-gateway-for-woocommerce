<?php

namespace BmWoocommerceVendor\WPDesk\License\Page;

use BmWoocommerceVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use BmWoocommerceVendor\WPDesk\License\InstalledPlugins;
/**
 * Can render and manage license page.
 *
 * @package WPDesk\License\Page\License
 */
class LicensePage implements \BmWoocommerceVendor\WPDesk\PluginBuilder\Plugin\Hookable
{
    const PAGE_SLUG = 'wpdesk-licenses';
    /** @var string Css/Js version */
    private $scripts_version = '1';
    /** @var InstalledPlugins */
    private $plugin_database;
    public function __construct(\BmWoocommerceVendor\WPDesk\License\InstalledPlugins $plugin_database)
    {
        $this->plugin_database = $plugin_database;
    }
    /**
     * Attach license page hooks.
     *
     * @return void
     */
    public function hooks()
    {
        \add_action('wp_ajax_wpdesk_api_hide_message', [$this, 'handle_api_hide_message']);
        \add_action('admin_enqueue_scripts', [$this, 'handle_css_scripts'], 100);
    }
    /**
     * Adds license page submenu.
     * Have to be called from admin_menu action.
     *
     * @return void
     */
    public function handle_add_page_submenu_item()
    {
        \add_submenu_page('wpdesk-helper', \__('Subscriptions', 'bm-woocommerce'), \__('Subscriptions', 'bm-woocommerce'), 'manage_options', self::PAGE_SLUG, [$this, 'handle_render_wpdesk_licenses_page']);
    }
    /**
     * Renders license page.
     *
     * @return void
     */
    public function handle_render_wpdesk_licenses_page()
    {
        global $wpdesk_helper_plugins;
        if (!isset($wpdesk_helper_plugins)) {
            $wpdesk_helper_plugins = [];
        }
        if (isset($_POST['plugin']) && $_POST['action']) {
            $this->execute_plugin_action(\sanitize_text_field($_POST['plugin']), \sanitize_key($_POST['action']));
        }
        $plugins = $this->plugin_database->get_plugins_activation_info();
        /** @noinspection PhpUnusedLocalVariableInspection */
        $plugins = $this->ensure_unique_product($plugins);
        if (!\class_exists('BmWoocommerceVendor\\WPDesk_Helper_List_Table')) {
            require_once __DIR__ . '/License/views/class-wpdesk-helper-list-table.php';
        }
        include __DIR__ . '/License/views/licenses.php';
    }
    /**
     * Ensures that no product are shown more than once
     *
     * @param array $plugins
     *
     * @return array
     */
    private function ensure_unique_product(array $plugins)
    {
        $uniqueness = [];
        return \array_filter($plugins, static function ($item) use(&$uniqueness) {
            $key = $item['product_id'];
            if (!isset($uniqueness[$key])) {
                $uniqueness[$key] = \true;
                return \true;
            }
            return \false;
        });
    }
    /**
     * Find plugin with given name and execute action with given name.
     *
     * @param $plugin string Plugin name
     * @param $action string to execute
     */
    private function execute_plugin_action($plugin, $action)
    {
        $plugins = $this->plugin_database->get_plugins_activation_info();
        foreach ($plugins as $plugin_key => $wpdesk_helper_plugin) {
            if ($wpdesk_helper_plugin['plugin'] === $plugin) {
                $plugin_info = $wpdesk_helper_plugin;
            }
        }
        if (isset($plugin_info)) {
            (new \BmWoocommerceVendor\WPDesk\License\Page\LicensePageActions())->create_action($action)->execute($plugin_info);
        }
    }
    /**
     * Remember that the given in request message should be closed.
     * Have to be called from wp_ajax_wpdesk_api_hide_message action.
     */
    public function handle_api_hide_message()
    {
        if (\wp_verify_nonce($_REQUEST['nonce'], 'wpdesk-api-ajax-notification-nonce')) {
            if (\update_option('wpdesk_api_message_close', \sanitize_key($_REQUEST['value']))) {
                die('1');
            }
            die('0');
        }
    }
    /**
     * Append license page css.
     *
     * Have to be called from admin_enqueue_scripts action.
     */
    public function handle_css_scripts()
    {
        $screen = \get_current_screen();
        if (isset($screen) && \in_array($screen->base, ['toplevel_page_wpdesk-helper', 'wp-desk_page_wpdesk-licenses', 'wp-desk-1_page_wpdesk-licenses'], \true)) {
            \wp_register_style(self::PAGE_SLUG, \plugins_url('wpdesk-helper/assets/css/admin-settings.css'), [], $this->scripts_version);
            \wp_enqueue_style(self::PAGE_SLUG);
        }
    }
}
