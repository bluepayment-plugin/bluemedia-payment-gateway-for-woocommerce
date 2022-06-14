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
if (!\class_exists('BmWoocommerceVendor\\WPDesk_Tracker_Data_Provider_Identification_Gdpr')) {
    /**
     * Class WPDesk_Tracker_Data_Provider_Identification_Gdpr
     */
    class WPDesk_Tracker_Data_Provider_Identification_Gdpr implements \WPDesk_Tracker_Data_Provider
    {
        const DATA_KEY_DOMAIN_HASH = 'domain_hash';
        const DATA_KEY_UNIQUE_HASH = 'unique_hash';
        const DATA_KEY_EMAIL_HASH = 'email_hash';
        const WPDESK_TRACKER_UNIQUE_HASH_OPTION_NAME = 'wpdesk_tracker_unique_hash';
        /**
         * Hash unique for wordpress instance.
         *
         * @return string
         */
        private function get_unique_hash()
        {
            $hash = \get_option(self::WPDESK_TRACKER_UNIQUE_HASH_OPTION_NAME, \false);
            if (!$hash) {
                $hash = \md5(\uniqid() . \NONCE_SALT);
                \update_option(self::WPDESK_TRACKER_UNIQUE_HASH_OPTION_NAME, $hash);
            }
            return $hash;
        }
        /**
         * Get info that allows anonymous data rollup.
         *
         * @return array Data provided to tracker.
         */
        public function get_data()
        {
            $data = [];
            $data[self::DATA_KEY_DOMAIN_HASH] = \md5(\home_url() . \NONCE_SALT);
            $data[self::DATA_KEY_UNIQUE_HASH] = $this->get_unique_hash();
            $data[self::DATA_KEY_EMAIL_HASH] = \md5(\apply_filters('wpdesk_tracker_admin_email', \get_option('admin_email')) . \NONCE_SALT);
            return $data;
        }
    }
}
