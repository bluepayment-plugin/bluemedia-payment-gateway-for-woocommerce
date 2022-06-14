<?php

namespace BmWoocommerceVendor\WPDesk\PluginBuilder\Plugin;

/**
 * It means that this class is should know about SUBSCRIPTION activation
 *
 * @package WPDesk\PluginBuilder\Plugin
 */
interface ActivationAware
{
    /**
     * Set the activation flag to true
     *
     * @return void
     */
    public function set_active();
    /**
     * Is subscription active?
     *
     * @return bool
     */
    public function is_active();
}
