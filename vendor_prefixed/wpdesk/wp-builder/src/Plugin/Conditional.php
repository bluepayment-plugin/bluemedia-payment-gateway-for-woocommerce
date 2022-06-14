<?php

namespace BmWoocommerceVendor\WPDesk\PluginBuilder\Plugin;

/**
 * Something that can be instantiated/hooked conditionally.
 *
 * @see https://github.com/mwpd/basic-scaffold/blob/master/src/Infrastructure/Conditional.php by Alain Schlesser
 *
 * @package WPDesk\PluginBuilder\Plugin
 */
interface Conditional
{
    /**
     * Check whether the conditional object is currently needed.
     *
     * @return bool Whether the conditional object is needed.
     */
    public static function is_needed();
}
