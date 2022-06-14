<?php

namespace BmWoocommerceVendor\WPDesk\Plugin\Flow\Initialization\Simple;

/**
 * Trait helps with tracker initialization
 *
 * @package WPDesk\Plugin\Flow\Initialization\Simple\
 */
trait TrackerInstanceAsFilterTrait
{
    /** @var \WPDesk_Tracker_Interface */
    private static $tracker_instance;
    /**
     * Returns filter action name for tracker instance
     *
     * @return string
     */
    private function get_tracker_action_name()
    {
        return 'wpdesk_tracker_instance';
    }
    /**
     * Returns version of the tracker. Inc when trackker is changed and should be instantiated fist.
     *
     * @return int
     */
    private function get_tracker_version()
    {
        return 2;
    }
    /**
     * @return \WPDesk_Tracker_Interface
     */
    private function get_tracker_instance()
    {
        return \apply_filters($this->get_tracker_action_name(), null);
    }
    /**
     * Prepare tracker to be instantiated using wpdesk_tracker_instance filter
     *
     * @return void|\WPDesk_Tracker
     */
    private function prepare_tracker_action()
    {
        \class_exists(\WPDesk_Tracker_Factory::class);
        //autoload this class
        \add_filter($this->get_tracker_action_name(), function ($tracker_instance) {
            if (\is_object($tracker_instance)) {
                return $tracker_instance;
            }
            if (\is_object(self::$tracker_instance)) {
                return self::$tracker_instance;
            }
            if (\apply_filters('wpdesk_can_start_tracker', \true, $this->plugin_info)) {
                $tracker_factory = new \BmWoocommerceVendor\WPDesk_Tracker_Factory_Prefixed();
                self::$tracker_instance = $tracker_factory->create_tracker(\basename($this->plugin_info->get_plugin_file_name()));
                \do_action('wpdesk_tracker_started', self::$tracker_instance, $this->plugin_info);
                return self::$tracker_instance;
            }
        }, 10 - $this->get_tracker_version());
    }
}
