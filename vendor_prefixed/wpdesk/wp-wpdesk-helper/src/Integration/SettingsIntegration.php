<?php

namespace BmWoocommerceVendor\WPDesk\Helper\Integration;

use BmWoocommerceVendor\WPDesk\Helper\Page\SettingsPage;
use BmWoocommerceVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use BmWoocommerceVendor\WPDesk\PluginBuilder\Plugin\HookableCollection;
use BmWoocommerceVendor\WPDesk\PluginBuilder\Plugin\HookableParent;
/**
 * Integrates WP Desk main settings page with WordPress
 *
 * @package WPDesk\Helper
 */
class SettingsIntegration implements \BmWoocommerceVendor\WPDesk\PluginBuilder\Plugin\Hookable, \BmWoocommerceVendor\WPDesk\PluginBuilder\Plugin\HookableCollection
{
    use HookableParent;
    /** @var SettingsPage */
    private $settings_page;
    public function __construct(\BmWoocommerceVendor\WPDesk\Helper\Page\SettingsPage $settingsPage)
    {
        $this->add_hookable($settingsPage);
    }
    /**
     * @return void
     */
    public function hooks()
    {
        $this->hooks_on_hookable_objects();
    }
}
