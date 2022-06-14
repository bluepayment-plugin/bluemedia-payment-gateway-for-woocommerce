<?php

namespace BmWoocommerceVendor\WPDesk\PluginBuilder\Plugin;

/**
 * Tag the plugin with this ingterface to hook it to the WordPress activation hook.
 *
 * Note: works from plugin flow ^2.2.
 *
 * @package WPDesk\PluginBuilder\Plugin
 */
interface Activateable
{
    /**
     * Plugin activated in WordPress. Do not execute directly.
     *
     * @return void
     */
    public function activate();
}
