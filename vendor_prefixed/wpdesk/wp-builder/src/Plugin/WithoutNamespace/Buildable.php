<?php

namespace BmWoocommerceVendor;

/**
 * Have info about what class should be built by WPDesk_Builder
 *
 * have to be compatible with PHP 5.2.x
 */
interface WPDesk_Buildable
{
    /** @return string */
    public function get_class_name();
}
