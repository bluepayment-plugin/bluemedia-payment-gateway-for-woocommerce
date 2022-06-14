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
if (!\class_exists('BmWoocommerceVendor\\WPDesk_Tracker_Data_Provider_Theme')) {
    /**
     * Class WPDesk_Tracker_Data_Provider_Theme
     */
    class WPDesk_Tracker_Data_Provider_Theme implements \WPDesk_Tracker_Data_Provider
    {
        /**
         * Get the current theme info, theme name and version.
         *
         * @return array Data provided to tracker.
         */
        public function get_data()
        {
            $theme_data = \wp_get_theme();
            $theme_child_theme = \is_child_theme() ? 'Yes' : 'No';
            return ['theme' => array('name' => $theme_data->Name, 'version' => $theme_data->Version, 'child_theme' => $theme_child_theme)];
        }
    }
}
