<?php

namespace BmWoocommerceVendor\WPDesk\PluginBuilder\Plugin;

interface Hookable
{
    /**
     * Init hooks (actions and filters).
     *
     * @return void
     */
    public function hooks();
}
