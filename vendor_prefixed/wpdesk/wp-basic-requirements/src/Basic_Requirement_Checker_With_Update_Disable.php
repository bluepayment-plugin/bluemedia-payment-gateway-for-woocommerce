<?php

namespace BmWoocommerceVendor;

if (!\class_exists('BmWoocommerceVendor\\WPDesk_Basic_Requirement_Checker')) {
    require_once 'Basic_Requirement_Checker.php';
}
if (!\class_exists('BmWoocommerceVendor\\WPDesk_Basic_Requirement_Checker_With_Update_Disable')) {
    /**
     * Checks requirements for plugin. When required plugin is updated right now, then say that requirements are not met temporary.
     * have to be compatible with PHP 5.2.x
     */
    class WPDesk_Basic_Requirement_Checker_With_Update_Disable extends \BmWoocommerceVendor\WPDesk_Basic_Requirement_Checker
    {
        /**
         * Returns true if are requirements are met.
         *
         * @return bool
         */
        public function are_requirements_met()
        {
            $has_been_met = parent::are_requirements_met();
            if (!$has_been_met) {
                return $has_been_met;
            }
            foreach ($this->plugin_require as $name => $plugin_info) {
                if ($this->is_currently_updated($name)) {
                    $nice_name = $plugin_info[self::PLUGIN_INFO_KEY_NICE_NAME];
                    $this->notices[] = $this->prepare_notice_message(\sprintf(\__('The &#8220;%s&#8221; plugin disables temporarily as required %s plugin is being upgraded.', $this->get_text_domain()), $this->plugin_name, $nice_name, $nice_name));
                }
            }
            return \count($this->notices) === 0;
        }
        /**
         * Is plugin upgrading right now?
         *
         * @param string $name
         *
         * @return bool
         */
        private function is_currently_updated($name)
        {
            return isset($_GET['action']) && $_GET['action'] === 'upgrade-plugin' && $_GET['plugin'] === $name;
        }
    }
}
