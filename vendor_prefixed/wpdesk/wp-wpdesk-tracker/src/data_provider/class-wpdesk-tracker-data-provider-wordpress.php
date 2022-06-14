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
if (!\class_exists('BmWoocommerceVendor\\WPDesk_Tracker_Data_Provider_Wordpress')) {
    /**
     * Class WPDesk_Tracker_Data_Provider_Wordpress
     */
    class WPDesk_Tracker_Data_Provider_Wordpress implements \WPDesk_Tracker_Data_Provider
    {
        /**
         * Get WordPress related data.
         *
         * @return array Data provided to tracker.
         */
        public function get_data()
        {
            $wp_data = array();
            $memory = \wc_let_to_num(\WP_MEMORY_LIMIT);
            if (\function_exists('memory_get_usage')) {
                $system_memory = \wc_let_to_num(@\ini_get('memory_limit'));
                $memory = \max($memory, $system_memory);
            }
            $wp_data['memory_limit'] = \size_format($memory);
            $wp_data['debug_mode'] = \defined('WP_DEBUG') && \WP_DEBUG ? 'Yes' : 'No';
            $wp_data['locale'] = \get_locale();
            $wp_data['version'] = \get_bloginfo('version');
            $wp_data['multisite'] = \is_multisite() ? 'Yes' : 'No';
            return ['wp' => $wp_data];
        }
    }
}
