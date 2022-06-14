<?php

namespace BmWoocommerceVendor\WPDesk\Helper;

use BmWoocommerceVendor\WPDesk\Notice\Notice;
/**
 * Know if helper is active/installed and can show notices about it
 *
 * @package WPDesk\Helper
 */
class HelperRemoveInfo
{
    private $plugin_file = 'wpdesk-helper/wpdesk-helper.php';
    /**
     * Is helper active? We should disable
     *
     * @return bool
     */
    public function is_helper_active()
    {
        return \BmWoocommerceVendor\WPDesk_Basic_Requirement_Checker::is_wp_plugin_active($this->plugin_file);
    }
    /**
     * Is helper installed? We should delete
     *
     * @return bool
     */
    public function is_helper_installed()
    {
        return \BmWoocommerceVendor\WPDesk_Basic_Requirement_Checker::is_wp_plugin_installed($this->plugin_file);
    }
    /**
     * Show notice with disable helper info and url
     *
     * @return void
     */
    public function show_deactivate_helper_notice()
    {
        $remove_url = \self_admin_url('plugins.php?action=deactivate&plugin=' . $this->plugin_file);
        if (\function_exists('wp_nonce_url') && \function_exists('wp_create_nonce')) {
            $remove_url = \wp_nonce_url($remove_url, 'deactivate-plugin_' . $this->plugin_file);
        }
        new \BmWoocommerceVendor\WPDesk\Notice\Notice(\sprintf(\__('We recommend to <a href="%s">deactivate and remove</a> the "WP Desk Helper" plugin as it is no longer required by WP Desk plugins', 'bm-woocommerce'), $remove_url));
    }
    /**
     * Show notice with remove helper info and url
     *
     * @return void
     */
    public function show_remove_helper_notice()
    {
        $remove_url = \self_admin_url('plugins.php?action=delete-selected&amp;checked[]=' . $this->plugin_file);
        if (\function_exists('wp_nonce_url') && \function_exists('wp_create_nonce')) {
            $remove_url = \wp_nonce_url($remove_url, 'bulk-plugins');
        }
        new \BmWoocommerceVendor\WPDesk\Notice\Notice(\sprintf(\__('We recommend to <a href="%s">remove</a> the "WP Desk Helper" plugin as it is no longer required by WP Desk plugins', 'bm-woocommerce'), $remove_url));
    }
}
