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
if (!\class_exists('BmWoocommerceVendor\\WPDesk_Tracker_Data_Provider_Products_Variations')) {
    /**
     * Class WPDesk_Tracker_Data_Provider_Products_Variations
     */
    class WPDesk_Tracker_Data_Provider_Products_Variations implements \WPDesk_Tracker_Data_Provider
    {
        /**
         * Info about numer of variations.
         *
         * @return array Data provided to tracker.
         */
        public function get_data()
        {
            $data['number_of_variations'] = 0;
            $number_of_variations = \wp_count_posts('product_variation');
            $data['number_of_variations'] = $number_of_variations;
            return $data;
        }
    }
}
