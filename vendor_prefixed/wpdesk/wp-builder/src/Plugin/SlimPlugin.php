<?php

namespace BmWoocommerceVendor\WPDesk\PluginBuilder\Plugin;

/**
 * Most clean plugin class with only most important details.
 */
abstract class SlimPlugin implements \BmWoocommerceVendor\WPDesk_Translatable
{
    /**
     * Initializes plugin external state.
     *
     * The plugin internal state is initialized in the constructor and the plugin should be internally consistent after creation.
     * The external state includes hooks execution, communication with other plugins, integration with WC etc.
     *
     * @return void
     */
    public abstract function init();
}
