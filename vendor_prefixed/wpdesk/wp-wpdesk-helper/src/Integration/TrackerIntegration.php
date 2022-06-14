<?php

namespace BmWoocommerceVendor\WPDesk\Helper\Integration;

use BmWoocommerceVendor\WPDesk\Helper\Page\SettingsPage;
use BmWoocommerceVendor\WPDesk\PluginBuilder\Plugin\Hookable;
/**
 * Integrates WP Desk tracker with WordPress
 *
 * @package WPDesk\Helper
 */
class TrackerIntegration implements \BmWoocommerceVendor\WPDesk\PluginBuilder\Plugin\Hookable
{
    /** @var SettingsPage */
    private $settings_page;
    /** @var \WPDesk_Tracker */
    private $tracker;
    public function __construct(\BmWoocommerceVendor\WPDesk\Helper\Page\SettingsPage $settings_page)
    {
        $this->settings_page = $settings_page;
    }
    public function hooks()
    {
        $tracker_enabled = \apply_filters('wpdesk_tracker_enabled', \true);
        if ($tracker_enabled) {
            \add_action('admin_init', function () {
                $this->handle_page_settings_track_section();
            });
            $this->tracker = $this->initialize_main_tracker();
        }
    }
    /**
     * @return \WPDesk_Tracker_Interface
     */
    private function initialize_main_tracker()
    {
        return \apply_filters('wpdesk_tracker_instance', null);
    }
    /**
     * @return \WPDesk_Tracker_Interface
     */
    public function get_tracker()
    {
        return $this->tracker;
    }
    /**
     * @return void
     */
    private function handle_page_settings_track_section()
    {
        \add_settings_section('wpdesk_helper_tracking', \__('Plugin usage tracking', 'bm-woocommerce'), null, $this->settings_page->get_page_name());
        \add_settings_field('wpdesk_tracker_agree', \__('Allow WP Desk to track plugin usage', 'bm-woocommerce'), function () {
            $this->handle_render_page_settings_track_section();
        }, $this->settings_page->get_page_name(), 'wpdesk_helper_tracking');
    }
    /**
     * @return void
     */
    private function handle_render_page_settings_track_section()
    {
        $options = $this->settings_page->get_saved_options();
        if (empty($options['wpdesk_tracker_agree'])) {
            $options['wpdesk_tracker_agree'] = '0';
        }
        ?>
        <input type="checkbox" id="wpdesk_helper_options[wpdesk_tracker_agree]"
               name="wpdesk_helper_options[wpdesk_tracker_agree]" value="1" <?php 
        \checked(1, $options['wpdesk_tracker_agree'], \true);
        ?>>
        <label for="wpdesk_helper_options[wpdesk_tracker_agree]"><?php 
        \_e('Enable', 'bm-woocommerce');
        ?></label>
        <p class="description" id="admin-email-description">
			<?php 
        $terms_url = \get_locale() === 'pl_PL' ? 'https://www.wpdesk.pl/dane-uzytkowania/' : 'https://www.wpdesk.net/usage-tracking/';
        \printf(\__('No sensitive data is tracked, %sread more%s.', 'bm-woocommerce'), '<a target="_blank" href="' . $terms_url . '">', '</a>');
        ?>
        </p>
		<?php 
    }
}
