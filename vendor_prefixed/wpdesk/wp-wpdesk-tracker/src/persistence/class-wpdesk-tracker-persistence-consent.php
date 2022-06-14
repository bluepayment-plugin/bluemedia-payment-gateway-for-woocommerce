<?php

namespace BmWoocommerceVendor;

class WPDesk_Tracker_Persistence_Consent
{
    /**
     * Option name with settings.
     * @var string
     */
    private $option_name = 'wpdesk_helper_options';
    /**
     * Checks if consent of tracking is active.
     *
     * @return bool Consent status.
     */
    public function is_active()
    {
        $options = \get_option($this->option_name, array());
        if (!\is_array($options)) {
            $options = array();
        }
        return isset($options['wpdesk_tracker_agree']) && $options['wpdesk_tracker_agree'] === '1';
    }
}
