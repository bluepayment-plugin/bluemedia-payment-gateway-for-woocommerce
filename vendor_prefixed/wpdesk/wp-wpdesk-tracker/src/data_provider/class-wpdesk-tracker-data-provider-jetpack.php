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
if (!\class_exists('BmWoocommerceVendor\\WPDesk_Tracker_Data_Provider_Jetpack')) {
    /**
     * Class WPDesk_Tracker_Data_Provider_Jetpack
     */
    class WPDesk_Tracker_Data_Provider_Jetpack implements \WPDesk_Tracker_Data_Provider
    {
        /**
         * Info about jetpack.
         *
         * @return array Data provided to tracker.
         */
        public function get_data()
        {
            $data = [];
            $data['jetpack_version'] = \defined('BmWoocommerceVendor\\JETPACK__VERSION') ? \BmWoocommerceVendor\JETPACK__VERSION : 'none';
            $data['jetpack_connected'] = \class_exists('BmWoocommerceVendor\\Jetpack') && \is_callable('Jetpack::is_active') && \BmWoocommerceVendor\Jetpack::is_active() ? 'yes' : 'no';
            $data['jetpack_is_staging'] = \class_exists('BmWoocommerceVendor\\Jetpack') && \is_callable('Jetpack::is_staging_site') && \BmWoocommerceVendor\Jetpack::is_staging_site() ? 'yes' : 'no';
            $data['connect_installed'] = \class_exists('BmWoocommerceVendor\\WC_Connect_Loader') ? 'yes' : 'no';
            $data['connect_active'] = \class_exists('BmWoocommerceVendor\\WC_Connect_Loader') && \wp_next_scheduled('wc_connect_fetch_service_schemas') ? 'yes' : 'no';
            return $data;
        }
    }
}
