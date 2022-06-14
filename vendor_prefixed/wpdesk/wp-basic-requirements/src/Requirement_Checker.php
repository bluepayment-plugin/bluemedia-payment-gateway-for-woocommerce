<?php

namespace BmWoocommerceVendor;

/**
 * Checks requirements for plugin
 * have to be compatible with PHP 5.2.x
 */
interface WPDesk_Requirement_Checker
{
    /**
     * @param string $version
     *
     * @return $this
     */
    public function set_min_php_require($version);
    /**
     * @param string $version
     *
     * @return $this
     */
    public function set_min_wp_require($version);
    /**
     * @param string $version
     *
     * @return $this
     */
    public function set_min_wc_require($version);
    /**
     * @param $version
     *
     * @return $this
     */
    public function set_min_openssl_require($version);
    /**
     * @param string $plugin_name
     * @param string $nice_plugin_name Nice plugin name for better looks in notice
     *
     * @return $this
     */
    public function add_plugin_require($plugin_name, $nice_plugin_name = null);
    /**
     * @param string $module_name
     * @param string $nice_name Nice module name for better looks in notice
     *
     * @return $this
     */
    public function add_php_module_require($module_name, $nice_name = null);
    /**
     * @param string $setting
     * @param mixed $value
     *
     * @return $this
     */
    public function add_php_setting_require($setting, $value);
    /**
     * @return bool
     */
    public function are_requirements_met();
    /**
     * @return void
     */
    public function disable_plugin_render_notice();
    /**
     * @return void
     */
    public function render_notices();
    /**
     * Renders requirement notices in admin panel
     *
     * @return void
     */
    public function disable_plugin();
}
