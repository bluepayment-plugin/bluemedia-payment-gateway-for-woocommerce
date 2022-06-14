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
    interface WPDesk_Tracker_Data_Provider
    {
        /**
         * Provides data
         *
         * @return array Data provided to tracker.
         */
        public function get_data();
    }
}
