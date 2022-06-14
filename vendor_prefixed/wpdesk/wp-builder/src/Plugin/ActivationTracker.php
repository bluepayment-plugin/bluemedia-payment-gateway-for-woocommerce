<?php

namespace BmWoocommerceVendor\WPDesk\PluginBuilder\Plugin;

/**
 * @deprecated nobody uses it :) And also this library is not a place for this class
 *
 * @package WPDesk\PluginBuilder\Plugin
 */
class ActivationTracker
{
    /**
     * Namespace.
     *
     * @var string
     */
    private $namespace;
    /**
     * ActivationTracker constructor.
     *
     * @param string $namespace Namespace for settings.
     */
    public function __construct($namespace)
    {
        $this->namespace = $namespace;
    }
    /**
     * Option name for date storage
     *
     * @return string
     */
    private function get_option_name_activation_date()
    {
        return $this->namespace . '_activation';
    }
    /**
     * Returns activation date and sets it if were not set before
     *
     * @return int unix timestamp for activation datetime
     */
    public function get_activation_date()
    {
        $activation_date = \get_option($this->get_option_name_activation_date());
        if (empty($activation_date)) {
            return $this->touch_activation_date();
        }
        return \intval($activation_date);
    }
    /**
     * Was activation more than two weeks before today
     *
     * @return bool
     */
    public function is_activated_more_than_two_weeks()
    {
        $two_weeks = 60 * 60 * 24 * 7 * 2;
        return $this->get_activation_date() + $two_weeks < \time();
    }
    /**
     * Sets activatiion date for today
     *
     * @return int unit timestamp for now
     */
    public function touch_activation_date()
    {
        $now = \time();
        \update_option($this->get_option_name_activation_date(), $now);
        return $now;
    }
}
