<?php

namespace BmWoocommerceVendor\WPDesk\License;

use BmWoocommerceVendor\WPDesk_API_Manager_With_Update_Flag;
/**
 * Provides info about activation/update state and can refresh that state
 *
 * @package WPDesk\License
 */
class InstalledPlugins
{
    const KEY_API_MANAGER = 'api_manager';
    const KEY_ACTIVATION_STATUS = 'activation_status';
    /**
     * Refresh WP info about updates.
     *
     * @return void
     */
    public function refresh_plugin_update_info()
    {
        $this->get_plugins_activation_info(\true);
    }
    /**
     * Returns info about activation/update state of plugins.
     *
     * @param bool $hook_to_updates If updates api should be called. If not sure then no!
     *
     * @return array Info about plugins.
     * Key is plugin name and values are plugin_info + KEY_API_MANAGER + KEY_ACTIVATION_STATUS
     */
    public function get_plugins_activation_info($hook_to_updates = \false)
    {
        global $wpdesk_helper_plugins;
        if (!isset($wpdesk_helper_plugins)) {
            $wpdesk_helper_plugins = [];
        }
        $plugins = [];
        foreach ($wpdesk_helper_plugins as $key => $wpdesk_helper_plugin) {
            $config_uri = null;
            if (isset($wpdesk_helper_plugin['config_uri'])) {
                $config_uri = $wpdesk_helper_plugin['config_uri'];
            }
            $menu_title = $wpdesk_helper_plugin['product_id'];
            if (isset($wpdesk_helper_plugin['title'])) {
                $menu_title = $wpdesk_helper_plugin['title'];
            }
            $addressRepository = new \BmWoocommerceVendor\WPDesk\License\ServerAddressRepository($wpdesk_helper_plugin['product_id']);
            $plugins[$key] = $wpdesk_helper_plugin;
            $plugins[$key][self::KEY_API_MANAGER] = new \BmWoocommerceVendor\WPDesk_API_Manager_With_Update_Flag($upgrade_url = $addressRepository->get_default_update_url(), $version = $wpdesk_helper_plugin['version'], $name = $wpdesk_helper_plugin['plugin'], $product_id = $wpdesk_helper_plugin['product_id'], $menu_title, $title = $menu_title, $plugin_file = \basename($wpdesk_helper_plugin['plugin']), $plugin_dir = \dirname($wpdesk_helper_plugin['plugin']), $config_uri, $hook_to_updates);
            $plugins[$key][self::KEY_ACTIVATION_STATUS] = \get_option($plugins[$key][self::KEY_API_MANAGER]->activated_key, 'Deactivated');
        }
        return $plugins;
    }
}
