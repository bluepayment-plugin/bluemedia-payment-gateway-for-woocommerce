<?php
/**
 * WP Desk Tracker
 *
 * @class        WPDESK_Tracker
 * @version        1.3.2
 * @package        WPDESK/Helper
 * @category    Class
 * @author        WP Desk
 */

/**
 * @deprecated Do not use. Only for purpose of compatibility with library 1.x version
 *
 * Class WPDesk_Tracker_Factory
 */
class WPDesk_Tracker_Factory
{
    /**
     * Creates tracker instance.
     *
     * @param string $basename Plugin basename.
     *
     * @return WPDesk_Tracker created tracker.
     */
    public function create_tracker($basename)
    {
        return apply_filters('wpdesk_tracker_instance', null);
    }
}