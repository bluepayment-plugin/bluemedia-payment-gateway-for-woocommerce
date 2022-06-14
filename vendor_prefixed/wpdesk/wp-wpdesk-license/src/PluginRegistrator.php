<?php

namespace BmWoocommerceVendor\WPDesk\License;

use BmWoocommerceVendor\WPDesk_Plugin_Info;
/**
 * Replaces WPDesk_Helper_Plugin. Gets info from plugin and sends it to subscription/update integrations
 *
 * @package WPDesk\License
 */
class PluginRegistrator
{
    /** @var WPDesk_Plugin_Info */
    private $plugin_info;
    public function __construct(\BmWoocommerceVendor\WPDesk_Plugin_Info $info)
    {
        $this->plugin_info = $info;
    }
    /**
     * @return bool
     */
    public function is_active()
    {
        return \get_option($this->get_activation_key(), \true) === 'Activated';
    }
    /**
     * @return string
     */
    /**
     * @return string
     */
    private function get_activation_key()
    {
        return 'api_' . \basename($this->plugin_info->get_plugin_dir()) . '_activated';
    }
    /**
     * Get plugin name in dir/file.php format
     *
     * @return string
     */
    private function get_plugin_wordpress_name()
    {
        return \basename($this->plugin_info->get_plugin_dir()) . '/' . \basename($this->plugin_info->get_plugin_file_name());
    }
    /**
     * Push info about plugin to the subscription libraries
     *
     * @TODO: change when wp-wpdesk-license migrate to 2.0
     *
     * @return void
     */
    public function add_plugin_to_installed_plugins()
    {
        global $wpdesk_helper_plugins;
        if (!\is_array($wpdesk_helper_plugins)) {
            $wpdesk_helper_plugins = [];
        }
        if (!$this->is_plugin_already_registered($wpdesk_helper_plugins, $this->plugin_info->get_product_id())) {
            $wpdesk_helper_plugins[] = $this->get_plugin_data();
        }
    }
    /**
     * @param array  $wpdesk_helper_plugins
     * @param string $plugin_id
     *
     * @return bool
     */
    private function is_plugin_already_registered(array $wpdesk_helper_plugins, $plugin_id)
    {
        return \array_reduce($wpdesk_helper_plugins, static function ($carry, $plugin_data) use($plugin_id) {
            return $carry || $plugin_data['product_id'] === $plugin_id;
        }, \false);
    }
    /**
     * @return array
     */
    private function get_plugin_data()
    {
        return ['plugin' => $this->get_plugin_wordpress_name(), 'product_id' => $this->plugin_info->get_product_id(), 'version' => $this->plugin_info->get_version(), 'config_uri' => ''];
    }
}
