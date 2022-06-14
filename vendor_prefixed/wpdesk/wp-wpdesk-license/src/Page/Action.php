<?php

namespace BmWoocommerceVendor\WPDesk\License\Page;

/**
 * Action that can be executed relative to plugin.
 *
 * @package WPDesk\License\Page
 */
interface Action
{
    public function execute(array $plugin);
}
