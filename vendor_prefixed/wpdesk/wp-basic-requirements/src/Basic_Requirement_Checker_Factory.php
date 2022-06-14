<?php

namespace BmWoocommerceVendor;

if (!\class_exists('BmWoocommerceVendor\\Basic_Requirement_Checker')) {
    require_once 'Basic_Requirement_Checker.php';
}
if (!\class_exists('BmWoocommerceVendor\\WPDesk_Basic_Requirement_Checker_With_Update_Disable')) {
    require_once 'Basic_Requirement_Checker_With_Update_Disable.php';
}
/**
 * Falicitates createion of requirement checker
 */
class WPDesk_Basic_Requirement_Checker_Factory
{
    const LIBRARY_TEXT_DOMAIN = 'bm-woocommerce';
    /**
     * Creates a simplest possible version of requirement checker.
     *
     * @param string $plugin_file
     * @param string $plugin_name
     * @param string|null $text_domain Text domain to use. If null try to use library text domain.
     *
     * @return WPDesk_Requirement_Checker
     */
    public function create_requirement_checker($plugin_file, $plugin_name, $text_domain = null)
    {
        return new \BmWoocommerceVendor\WPDesk_Basic_Requirement_Checker($plugin_file, $plugin_name, $text_domain, null, null);
    }
    /**
     * Creates a requirement checker according to given requirements array info.
     *
     * @param string $plugin_file
     * @param string $plugin_name
     * @param string $text_domain Text domain to use. If null try to use library text domain.
     * @param array $requirements Requirements array as given by plugin.
     *
     * @return WPDesk_Requirement_Checker
     */
    public function create_from_requirement_array($plugin_file, $plugin_name, $requirements, $text_domain = null)
    {
        $requirements_checker = new \BmWoocommerceVendor\WPDesk_Basic_Requirement_Checker_With_Update_Disable($plugin_file, $plugin_name, $text_domain, $requirements['php'], $requirements['wp']);
        if (isset($requirements['plugins'])) {
            foreach ($requirements['plugins'] as $requirement) {
                $version = isset($requirement['version']) ? $requirement['version'] : null;
                $requirements_checker->add_plugin_require($requirement['name'], $requirement['nice_name'], $version);
            }
            $requirements_checker->transient_delete_on_plugin_version_changed();
        }
        if (isset($requirements['repo_plugins'])) {
            foreach ($requirements['repo_plugins'] as $requirement) {
                $requirements_checker->add_plugin_repository_require($requirement['name'], $requirement['version'], $requirement['nice_name']);
            }
        }
        if (isset($requirements['modules'])) {
            foreach ($requirements['modules'] as $requirement) {
                $requirements_checker->add_php_module_require($requirement['name'], $requirement['nice_name']);
            }
        }
        return $requirements_checker;
    }
}
