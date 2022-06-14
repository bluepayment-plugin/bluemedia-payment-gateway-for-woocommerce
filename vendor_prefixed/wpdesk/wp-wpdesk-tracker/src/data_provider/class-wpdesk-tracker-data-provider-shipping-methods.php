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
if (!\class_exists('BmWoocommerceVendor\\WPDesk_Tracker_Data_Provider_Shipping_Methods')) {
    /**
     * Class WPDesk_Tracker_Data_Provider_Shipping_Methods
     */
    class WPDesk_Tracker_Data_Provider_Shipping_Methods implements \WPDesk_Tracker_Data_Provider
    {
        /**
         * Get a list of all active shipping methods.
         *
         * @return array Data provided to tracker.
         */
        public function get_data()
        {
            $active_methods = array();
            $shipping_methods = \WC()->shipping->get_shipping_methods();
            foreach ($shipping_methods as $id => $shipping_method) {
                if (isset($shipping_method->enabled) && 'yes' === $shipping_method->enabled) {
                    $active_methods[$id] = array('title' => $shipping_method->title, 'tax_status' => $shipping_method->tax_status);
                }
            }
            return ['shipping_methods' => $active_methods];
        }
    }
}
