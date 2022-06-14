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
if (!\class_exists('BmWoocommerceVendor\\WPDesk_Tracker_Data_Provider_Users')) {
    /**
     * Class WPDesk_Tracker_Data_Provider_Users
     */
    class WPDesk_Tracker_Data_Provider_Users implements \WPDesk_Tracker_Data_Provider
    {
        /**
         * Get user totals based on user role.
         *
         * @return array Data provided to tracker.
         */
        public function get_data()
        {
            $user_count = array();
            $user_count_data = \count_users();
            $user_count['total'] = $user_count_data['total_users'];
            // Get user count based on user role
            foreach ($user_count_data['avail_roles'] as $role => $count) {
                $user_count[$role] = $count;
            }
            return ['users' => $user_count];
        }
    }
}
