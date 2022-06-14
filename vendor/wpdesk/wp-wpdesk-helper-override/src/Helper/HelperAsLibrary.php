<?php

namespace WPDesk\Helper;

/**
 * @deprecated Do not use. Only for purpose of compatibility with library 1.x version
 *
 * @package WPDesk\Helper
 */
class HelperAsLibrary
{
    public function hooks()
    {
        do_action('wpdesk_helper_instance');
    }
    /**
     * @return \WPDesk_Tracker
     */
    public function get_tracker()
    {
        return apply_filters('wpdesk_tracker_instance', null);
    }
    /**
     * @return LoggerInterface
     */
    public function get_logger()
    {
        return apply_filters('wpdesk_logger_instance', null);
    }
}
