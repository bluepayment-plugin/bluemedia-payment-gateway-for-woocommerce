<?php

namespace BmWoocommerceVendor;

interface WPDesk_Requirement_Checker_Factory
{
    /**
     * @param $plugin_file
     * @param $plugin_name
     * @param $text_domain
     * @param $php_version
     * @param $wp_version
     *
     * @return WPDesk_Requirement_Checker
     */
    public function create_requirement_checker($plugin_file, $plugin_name, $text_domain);
}
