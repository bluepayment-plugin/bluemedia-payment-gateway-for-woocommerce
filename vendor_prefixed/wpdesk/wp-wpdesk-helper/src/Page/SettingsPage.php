<?php

namespace BmWoocommerceVendor\WPDesk\Helper\Page;

use BmWoocommerceVendor\WPDesk\PluginBuilder\Plugin\Hookable;
/**
 * Can render and manage license page.
 *
 * @package WPDesk\Helper
 */
class SettingsPage implements \BmWoocommerceVendor\WPDesk\PluginBuilder\Plugin\Hookable
{
    const PRIORITY_AFTER_LICENSE = 25;
    public function hooks()
    {
        \add_action('admin_init', function () {
            $this->handle_register_settings();
        });
        \add_action('admin_menu', function () {
            $this->handle_add_settings_menu();
        }, self::PRIORITY_AFTER_LICENSE);
    }
    /**
     * Register WordPress settings that can be used on settings page
     *
     * @return void
     */
    private function handle_register_settings()
    {
        \register_setting($this->get_option_name(), $this->get_option_name());
    }
    /**
     * Unique settings option id
     *
     * @return string
     */
    public function get_option_name()
    {
        return 'wpdesk_helper_options';
    }
    /**
     * @return void
     */
    private function handle_add_settings_menu()
    {
        \add_submenu_page('wpdesk-helper', \__('Settings', 'bm-woocommerce'), \__('Settings', 'bm-woocommerce'), 'manage_options', 'wpdesk-helper-settings', function () {
            $this->handle_render_wpdesk_helper_settings();
        });
    }
    /**
     * @return void
     */
    private function handle_render_wpdesk_helper_settings()
    {
        ?>
        <div class="wrap">
            <h1><?php 
        \_e('WP Desk Helper Settings', 'bm-woocommerce');
        ?></h1>
            <form method="post" action="options.php">
				<?php 
        \settings_fields('wpdesk_helper_options');
        \do_settings_sections($this->get_page_name());
        \submit_button();
        ?>
            </form>
        </div>
		<?php 
    }
    /**
     * Unique page id
     *
     * @return string
     */
    public function get_page_name()
    {
        return 'wpdesk_helper';
    }
    /**
     * Options saved in settings as array
     *
     * @return array
     */
    public function get_saved_options()
    {
        $options = \get_option($this->get_option_name(), []);
        if (!\is_array($options)) {
            $options = [];
        }
        return $options;
    }
}
