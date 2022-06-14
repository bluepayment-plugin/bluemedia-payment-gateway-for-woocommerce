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
if (!\class_exists('BmWoocommerceVendor\\WPDesk_Tracker_Data_Provider_Server')) {
    /**
     * Class WPDesk_Tracker_Data_Provider_Server
     */
    class WPDesk_Tracker_Data_Provider_Server implements \WPDesk_Tracker_Data_Provider
    {
        /**
         * Info about bawic server data.
         *
         * @return array Data provided to tracker.
         */
        public function get_data()
        {
            $server_data = array();
            if (isset($_SERVER['SERVER_SOFTWARE']) && !empty($_SERVER['SERVER_SOFTWARE'])) {
                $server_data['software'] = $_SERVER['SERVER_SOFTWARE'];
            }
            if (\function_exists('phpversion')) {
                $server_data['php_version'] = \phpversion();
            }
            if (\function_exists('ini_get')) {
                $server_data['php_post_max_size'] = \size_format(\wc_let_to_num(\ini_get('post_max_size')));
                $server_data['php_time_limt'] = \ini_get('max_execution_time');
                $server_data['php_max_input_vars'] = \ini_get('max_input_vars');
                $server_data['php_suhosin'] = \extension_loaded('suhosin') ? 'Yes' : 'No';
            }
            global $wpdb;
            $server_data['mysql_version'] = $wpdb->db_version();
            $server_data['php_max_upload_size'] = \size_format(\wp_max_upload_size());
            $server_data['php_default_timezone'] = \date_default_timezone_get();
            $server_data['php_soap'] = \class_exists('SoapClient') ? 'Yes' : 'No';
            $server_data['php_fsockopen'] = \function_exists('fsockopen') ? 'Yes' : 'No';
            $server_data['php_curl'] = \function_exists('curl_init') ? 'Yes' : 'No';
            return ['server' => $server_data];
        }
    }
}
