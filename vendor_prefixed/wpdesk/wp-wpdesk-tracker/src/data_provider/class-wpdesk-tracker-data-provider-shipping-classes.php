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
if (!\class_exists('BmWoocommerceVendor\\WPDesk_Tracker_Data_Provider_Shipping_Classes')) {
    /**
     * Class WPDesk_Tracker_Data_Provider_Shipping_Classes
     */
    class WPDesk_Tracker_Data_Provider_Shipping_Classes implements \WPDesk_Tracker_Data_Provider
    {
        /**
         * Info about number of shipping classes
         *
         * @return array Data provided to tracker.
         */
        public function get_data()
        {
            $data['number_of_shipping_classes'] = 0;
            $shipping_classes = \WC()->shipping()->get_shipping_classes();
            if (\is_array($shipping_classes)) {
                $data['number_of_shipping_classes'] = \count($shipping_classes);
            }
            return $data;
        }
    }
}
