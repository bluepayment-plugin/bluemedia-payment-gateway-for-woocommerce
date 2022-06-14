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
if (!\class_exists('BmWoocommerceVendor\\WPDesk_Tracker_Data_Provider_License_Emails')) {
    /**
     * Class WPDesk_Tracker_Data_Provider_License_Emails
     */
    class WPDesk_Tracker_Data_Provider_License_Emails implements \WPDesk_Tracker_Data_Provider
    {
        /**
         * Info about license emails from api manager.
         *
         * @return array Data provided to tracker.
         */
        public function get_data()
        {
            global $wpdesk_helper_plugins;
            $license_emails_email = array();
            $license_emails = array();
            if (!isset($wpdesk_helper_plugins)) {
                $wpdesk_helper_plugins = array();
            }
            foreach ($wpdesk_helper_plugins as $key => $plugin) {
                if (isset($plugin['api_manager'])) {
                    $api_manager = $plugin['api_manager'];
                    if (isset($api_manager->options[$api_manager->activation_email])) {
                        $license_emails_email[$api_manager->options[$api_manager->activation_email]] = $api_manager->options[$api_manager->activation_email];
                    }
                }
            }
            foreach ($license_emails_email as $email) {
                $license_emails[] = $email;
            }
            $data['license_emails'] = $license_emails;
            return $data;
        }
    }
}
