<?php

namespace BmWoocommerceVendor;

/**
 * WP Desk Tracker
 *
 * @class        WPDESK_Tracker
 * @version        1.3.2
 * @package        WPDESK/Helper
 * @category    Class
 * @author        WP Desk
 */
if (!\defined('ABSPATH')) {
    exit;
}
if (!\class_exists('WPDesk_Tracker_Data_Provider')) {
    /**
     * Class WPDesk_Tracker_Data_Provider_Plugins
     */
    class WPDesk_Tracker_Data_Provider_Plugins implements \WPDesk_Tracker_Data_Provider
    {
        /**
         * Get all plugins grouped into activated or not.
         *
         * @return array
         */
        private function get_all_plugins()
        {
            // Ensure get_plugins function is loaded
            if (!\function_exists('get_plugins')) {
                include \ABSPATH . '/wp-admin/includes/plugin.php';
            }
            $plugins = \get_plugins();
            $active_plugins_keys = \get_option('active_plugins', array());
            $active_plugins = array();
            foreach ($plugins as $k => $v) {
                // Take care of formatting the data how we want it.
                $formatted = array();
                $formatted['name'] = \strip_tags($v['Name']);
                if (isset($v['Version'])) {
                    $formatted['version'] = \strip_tags($v['Version']);
                }
                if (isset($v['Author'])) {
                    $formatted['author'] = \strip_tags($v['Author']);
                }
                if (isset($v['Network'])) {
                    $formatted['network'] = \strip_tags($v['Network']);
                }
                if (isset($v['PluginURI'])) {
                    $formatted['plugin_uri'] = \strip_tags($v['PluginURI']);
                }
                if (\in_array($k, $active_plugins_keys)) {
                    // Remove active plugins from list so we can show active and inactive separately
                    unset($plugins[$k]);
                    $active_plugins[$k] = $formatted;
                } else {
                    $plugins[$k] = $formatted;
                }
            }
            return array('active_plugins' => $active_plugins, 'inactive_plugins' => $plugins);
        }
        /**
         * Provides data
         *
         * @return array Data provided to tracker.
         */
        public function get_data()
        {
            $data = [];
            $all_plugins = $this->get_all_plugins();
            $data['active_plugins'] = $all_plugins['active_plugins'];
            $data['inactive_plugins'] = $all_plugins['inactive_plugins'];
            return $data;
        }
    }
}
