<?php

namespace BmWoocommerceVendor\WPDesk\Helper\Page;

use BmWoocommerceVendor\WPDesk\Helper\Debug\LibraryDebug;
use BmWoocommerceVendor\WPDesk\PluginBuilder\Plugin\Hookable;
/**
 * Adds library debug page to the admin panel
 *
 * @package WPDesk\Helper
 */
class LibraryDebugPage implements \BmWoocommerceVendor\WPDesk\PluginBuilder\Plugin\Hookable
{
    const PRIOTITY_LONG_AFTER_ALL_IS_LOADED = 999;
    /** @var LibraryDebug */
    private $library_debug;
    public function __construct(\BmWoocommerceVendor\WPDesk\Helper\Debug\LibraryDebug $library_debug)
    {
        $this->library_debug = $library_debug;
    }
    public function hooks()
    {
        \add_action('admin_menu', function () {
            $menu_visible = $this->is_wpdesk_user();
            $parent_slug = null;
            if ($menu_visible) {
                $parent_slug = 'wpdesk-helper';
            }
            \add_submenu_page($parent_slug, \__('Library report', 'bm-woocommerce'), \__('Library report', 'bm-woocommerce'), 'manage_options', 'wpdesk-helper-library-report', function () {
                $this->handle_render_library_report_page();
            });
        }, self::PRIOTITY_LONG_AFTER_ALL_IS_LOADED);
    }
    /**
     * @return void
     */
    private function handle_render_library_report_page()
    {
        $vendor_files_report = $this->library_debug->get_wpdesk_vendor_files_report();
        echo '<pre>';
        /** @noinspection ForgottenDebugOutputInspection */
        \print_r(['used_libraries' => $this->library_debug->get_libraries_report($vendor_files_report), 'used_files' => $vendor_files_report]);
        echo '</pre>';
    }
    /**
     * Checks if user from WPDesk domain is logged in
     *
     * @return bool
     */
    private function is_wpdesk_user()
    {
        $userdata = \get_userdata(\get_current_user_id());
        return \preg_match('/@wpdesk\\..+/', $userdata->user_email) >= 1;
    }
}
