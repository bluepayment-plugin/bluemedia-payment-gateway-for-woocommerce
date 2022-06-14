<?php

namespace BmWoocommerceVendor\WPDesk\Notice;

/**
 * Class PermanentDismissibleNotice
 *
 * WordPress admin dismissible notice.
 * @package WPDesk\Notice
 */
class PermanentDismissibleNotice extends \BmWoocommerceVendor\WPDesk\Notice\Notice
{
    const OPTION_NAME_PREFIX = 'wpdesk_notice_dismiss_';
    const OPTION_VALUE_DISMISSED = '1';
    /**
     * @var string
     */
    private $noticeName;
    /**
     * @var string
     */
    private $noticeDismissOptionName;
    /**
     * WPDesk_Flexible_Shipping_Notice constructor.
     *
     * @param string $noticeContent Notice content.
     * @param string $noticeName Notice dismiss option name.
     * @param string $noticeType Notice type.
     * @param int    $priority Priority
     * @param array $attributes Attributes.
     */
    public function __construct($noticeContent, $noticeName, $noticeType = 'info', $priority = 10, $attributes = array())
    {
        parent::__construct($noticeContent, $noticeType, \true, $priority, $attributes);
        $this->noticeName = $noticeName;
        $this->noticeDismissOptionName = static::OPTION_NAME_PREFIX . $noticeName;
        if (self::OPTION_VALUE_DISMISSED === \get_option($this->noticeDismissOptionName, '')) {
            $this->removeAction();
        }
    }
    /**
     * Undo dismiss notice.
     */
    public function undoDismiss()
    {
        \delete_option($this->noticeDismissOptionName);
        $this->addAction();
    }
    /**
     * Get attributes as string.
     *
     * @return string
     */
    protected function getAttributesAsString()
    {
        $attributesAsString = parent::getAttributesAsString();
        $attributesAsString .= \sprintf(' data-notice-name="%1$s"', \esc_attr($this->noticeName));
        $attributesAsString .= \sprintf(' id="wpdesk-notice-%1$s"', \esc_attr($this->noticeName));
        return $attributesAsString;
    }
}
