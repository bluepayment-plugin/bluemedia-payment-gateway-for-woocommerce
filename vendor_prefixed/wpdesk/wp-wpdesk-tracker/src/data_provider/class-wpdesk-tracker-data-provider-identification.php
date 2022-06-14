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
if (!\class_exists('BmWoocommerceVendor\\WPDesk_Tracker_Data_Provider_Identification')) {
    /**
     * Class WPDesk_Tracker_Data_Provider_Identification
     */
    class WPDesk_Tracker_Data_Provider_Identification implements \WPDesk_Tracker_Data_Provider
    {
        /**
         * Get info that allows session identification.
         *
         * @return array Data provided to tracker.
         */
        public function get_data()
        {
            $data = [];
            $data['url'] = \home_url();
            $data['email'] = \apply_filters('wpdesk_tracker_admin_email', \get_option('admin_email'));
            return $data;
        }
    }
}
