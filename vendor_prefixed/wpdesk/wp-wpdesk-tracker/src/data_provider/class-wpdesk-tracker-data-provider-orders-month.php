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
if (!\class_exists('BmWoocommerceVendor\\WPDesk_Tracker_Data_Provider_Orders_Month')) {
    /**
     * Class WPDesk_Tracker_Data_Provider_Orders_Month
     */
    class WPDesk_Tracker_Data_Provider_Orders_Month implements \WPDesk_Tracker_Data_Provider
    {
        /**
         * Info about orders per month.
         *
         * @return array Data provided to tracker.
         */
        public function get_data()
        {
            global $wpdb;
            $query = $wpdb->get_results("\n            \tSELECT min(post_date) min, max(post_date) max, TIMESTAMPDIFF(MONTH, min(post_date), max(post_date) )+1 months\n            \tFROM {$wpdb->posts} p\n            \tWHERE p.post_type = 'shop_order'\n            \tAND p.post_status = 'wc-completed'\n            \t");
            $data['orders_per_month'] = array();
            if ($query) {
                foreach ($query as $row) {
                    $data['orders_per_month']['first'] = $row->min;
                    $data['orders_per_month']['last'] = $row->max;
                    $data['orders_per_month']['months'] = $row->months;
                    if ($row->months != 0) {
                        if (isset($data['orders']) && isset($data['orders']['wc-completed'])) {
                            $data['orders_per_month']['per_month'] = \floatval($data['orders']['wc-completed']) / \floatval($row->months);
                        }
                    }
                }
            }
            return $data;
        }
    }
}
