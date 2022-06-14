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
if (!\class_exists('BmWoocommerceVendor\\WPDesk_Tracker_Data_Provider_User_Agent')) {
    /**
     * Class WPDesk_Tracker_Data_Provider_User_Agent
     */
    class WPDesk_Tracker_Data_Provider_User_Agent implements \WPDesk_Tracker_Data_Provider
    {
        /**
         * When an admin user logs in, there user agent is tracked in user meta and collected here.
         *
         * @return array Data provided to tracker.
         */
        public function get_data()
        {
            return ['admin_user_agents' => \array_filter((array) \get_option('woocommerce_tracker_ua', array()))];
        }
    }
}
