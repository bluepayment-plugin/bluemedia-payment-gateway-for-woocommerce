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
if (!\class_exists('BmWoocommerceVendor\\WPDesk_Tracker_Data_Provider_Orders')) {
    /**
     * Class WPDesk_Tracker_Data_Provider_Orders
     */
    class WPDesk_Tracker_Data_Provider_Orders implements \WPDesk_Tracker_Data_Provider
    {
        /**
         * Get order counts based on order status.
         *
         * @return array Data provided to tracker.
         */
        public function get_data()
        {
            $order_count = array();
            $order_count_data = \wp_count_posts('shop_order');
            foreach (\wc_get_order_statuses() as $status_slug => $status_name) {
                $order_count[$status_slug] = $order_count_data->{$status_slug};
            }
            return ['orders' => $order_count];
        }
    }
}
