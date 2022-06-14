<?php

namespace BmWoocommerceVendor\WPDesk\PluginBuilder\Plugin;

/**
 * Tag the plugin with this ingterface to hook it to the WordPress deactivation hook.
 *
 * Note: works from plugin flow ^2.2.
 *
 * @package WPDesk\PluginBuilder\Plugin
 */
interface Deactivateable
{
    /**
     * Plugin deactivate in WordPress. Do not execute directly.
     *
     * @return void
     */
    public function deactivate();
}
