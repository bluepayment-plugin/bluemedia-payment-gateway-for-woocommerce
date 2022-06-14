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
if (!\class_exists('BmWoocommerceVendor\\WPDesk_Tracker_Data_Provider_Settings')) {
    /**
     * Class WPDesk_Tracker_Data_Provider_Settings
     */
    class WPDesk_Tracker_Data_Provider_Settings implements \WPDesk_Tracker_Data_Provider
    {
        /**
         *  Get all options starting with woocommerce_ prefix.
         *
         * @return array Data provided to tracker.
         */
        public function get_data()
        {
            return ['settings' => array('version' => \WC()->version, 'currency' => \get_woocommerce_currency(), 'base_location' => \WC()->countries->get_base_country(), 'selling_locations' => \WC()->countries->get_allowed_countries(), 'api_enabled' => \get_option('woocommerce_api_enabled'), 'weight_unit' => \get_option('woocommerce_weight_unit'), 'dimension_unit' => \get_option('woocommerce_dimension_unit'), 'download_method' => \get_option('woocommerce_file_download_method'), 'download_require_login' => \get_option('woocommerce_downloads_require_login'), 'calc_taxes' => \get_option('woocommerce_calc_taxes'), 'coupons_enabled' => \get_option('woocommerce_enable_coupons'), 'guest_checkout' => \get_option('woocommerce_enable_guest_checkout'), 'secure_checkout' => \get_option('woocommerce_force_ssl_checkout'), 'enable_signup_and_login_from_checkout' => \get_option('woocommerce_enable_signup_and_login_from_checkout'), 'enable_myaccount_registration' => \get_option('woocommerce_enable_myaccount_registration'), 'registration_generate_username' => \get_option('woocommerce_registration_generate_username'), 'registration_generate_password' => \get_option('woocommerce_registration_generate_password'))];
        }
    }
}
