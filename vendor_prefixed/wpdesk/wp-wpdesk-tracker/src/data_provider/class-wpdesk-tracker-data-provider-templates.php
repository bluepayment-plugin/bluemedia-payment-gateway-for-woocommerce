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
if (!\class_exists('BmWoocommerceVendor\\WPDesk_Tracker_Data_Provider_Templates')) {
    /**
     * Class WPDesk_Tracker_Data_Provider_Templates
     */
    class WPDesk_Tracker_Data_Provider_Templates implements \WPDesk_Tracker_Data_Provider
    {
        /**
         * Look for any template override and return filenames.
         *
         * @return array Data provided to tracker.
         */
        public function get_data()
        {
            $override_data = array();
            $template_paths = \apply_filters('woocommerce_template_overrides_scan_paths', array('WooCommerce' => \WC()->plugin_path() . '/templates/'));
            $scanned_files = array();
            require_once \WC()->plugin_path() . '/includes/admin/class-wc-admin-status.php';
            foreach ($template_paths as $plugin_name => $template_path) {
                $scanned_files[$plugin_name] = \WC_Admin_Status::scan_template_files($template_path);
            }
            foreach ($scanned_files as $plugin_name => $files) {
                foreach ($files as $file) {
                    if (\file_exists(\get_stylesheet_directory() . '/' . $file)) {
                        $theme_file = \get_stylesheet_directory() . '/' . $file;
                    } elseif (\file_exists(\get_stylesheet_directory() . '/woocommerce/' . $file)) {
                        $theme_file = \get_stylesheet_directory() . '/woocommerce/' . $file;
                    } elseif (\file_exists(\get_template_directory() . '/' . $file)) {
                        $theme_file = \get_template_directory() . '/' . $file;
                    } elseif (\file_exists(\get_template_directory() . '/woocommerce/' . $file)) {
                        $theme_file = \get_template_directory() . '/woocommerce/' . $file;
                    } else {
                        $theme_file = \false;
                    }
                    if (\false !== $theme_file) {
                        $override_data[] = \basename($theme_file);
                    }
                }
            }
            return ['template_overrides' => $override_data];
        }
    }
}
