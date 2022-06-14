<?php

namespace BmWoocommerceVendor\WPDesk\Notice;

/**
 * Class Factory
 *
 * Factory for notices.
 * @package WPDesk\Notice
 */
class Factory
{
    /**
     * Creates Notice object.
     *
     * @param string $noticeType Notice type.
     * @param string $noticeContent Notice content.
     * @param bool   $isDismissible Is dismissible.
     * @param int    $priority Priority.
     *
     * @return Notice
     */
    public static function notice($noticeContent = '', $noticeType = 'info', $isDismissible = \false, $priority = 10)
    {
        return new \BmWoocommerceVendor\WPDesk\Notice\Notice($noticeContent, $noticeType, $isDismissible, $priority);
    }
    /**
     * Creates PermanentDismissibleNotice object.
     *
     * @param string $noticeContent
     * @param string $noticeType
     * @param string $noticeName
     * @param int    $priority
     *
     * @return PermanentDismissibleNotice
     */
    public static function permanentDismissibleNotice($noticeContent = '', $noticeName = '', $noticeType = '', $priority = 10)
    {
        return new \BmWoocommerceVendor\WPDesk\Notice\PermanentDismissibleNotice($noticeContent, $noticeName, $noticeType, $priority);
    }
}
