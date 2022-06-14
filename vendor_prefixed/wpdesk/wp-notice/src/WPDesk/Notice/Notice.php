<?php

namespace BmWoocommerceVendor\WPDesk\Notice;

/**
 * Class Notice
 *
 * WordPress admin notice.
 * @package WPDesk\Notice
 */
class Notice
{
    const NOTICE_TYPE_ERROR = 'error';
    const NOTICE_TYPE_WARNING = 'warning';
    const NOTICE_TYPE_SUCCESS = 'success';
    const NOTICE_TYPE_INFO = 'info';
    const ADMIN_FOOTER_BASE_PRIORITY = 9999999;
    /**
     * Notice type.
     *
     * @var string
     */
    protected $noticeType;
    /**
     * Notice content.
     *
     * @var string
     */
    protected $noticeContent;
    /**
     * Is dismissible.
     *
     * @var bool
     */
    protected $dismissible;
    /**
     * Notice hook priority.
     * @var int;
     */
    protected $priority;
    /**
     * Is action added?
     * @var bool
     */
    private $actionAdded = \false;
    /**
     * Attributes.
     *
     * @var string[]
     */
    protected $attributes = array();
    /**
     * WPDesk_Flexible_Shipping_Notice constructor.
     *
     * @param string $noticeContent Notice content.
     * @param string $noticeType Notice type.
     * @param bool $dismissible Is dismissible.
     * @param int $priority Notice priority.
     * @param array $attributes Attributes.
     */
    public function __construct($noticeContent, $noticeType = 'info', $dismissible = \false, $priority = 10, $attributes = array())
    {
        $this->noticeContent = $noticeContent;
        $this->noticeType = $noticeType;
        $this->dismissible = $dismissible;
        $this->priority = $priority;
        $this->attributes = $attributes;
        $this->addAction();
    }
    /**
     * @return string
     */
    public function getNoticeContent()
    {
        return $this->noticeContent;
    }
    /**
     * @param string $noticeContent
     */
    public function setNoticeContent($noticeContent)
    {
        $this->noticeContent = $noticeContent;
    }
    /**
     * @return string
     */
    public function getNoticeType()
    {
        return $this->noticeType;
    }
    /**
     * @param string $noticeType
     */
    public function setNoticeType($noticeType)
    {
        $this->noticeType = $noticeType;
    }
    /**
     * @return bool
     */
    public function isDismissible()
    {
        return $this->dismissible;
    }
    /**
     * @param bool $dismissible
     */
    public function setDismissible($dismissible)
    {
        $this->dismissible = $dismissible;
    }
    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }
    /**
     * @param int $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
        if ($this->actionAdded) {
            $this->removeAction();
            $this->addAction();
        }
    }
    /**
     * Add notice action.
     */
    protected function addAction()
    {
        if (!$this->actionAdded) {
            \add_action('admin_notices', [$this, 'showNotice'], $this->priority);
            \add_action('admin_footer', [$this, 'showNotice'], self::ADMIN_FOOTER_BASE_PRIORITY + \intval($this->priority));
            $this->actionAdded = \true;
        }
    }
    /**
     * Remove action.
     */
    protected function removeAction()
    {
        if ($this->actionAdded) {
            \remove_action('admin_notices', [$this, 'showNotice'], $this->priority);
            \remove_action('admin_footer', [$this, 'showNotice'], self::ADMIN_FOOTER_BASE_PRIORITY + \intval($this->priority));
            $this->actionAdded = \false;
        }
    }
    /**
     * Add attribute.
     *
     * @param string $name Name
     * @param string $value Value.
     */
    public function addAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
    }
    /**
     * Get notice class.
     *
     * @return string
     */
    protected function getNoticeClass()
    {
        if ('updated' === $this->noticeType) {
            $notice_class = 'notice ' . $this->noticeType;
        } else {
            $notice_class = 'notice notice-' . $this->noticeType;
        }
        if ($this->dismissible) {
            $notice_class .= ' is-dismissible';
        }
        if (isset($this->attributes['class'])) {
            $notice_class .= ' ' . $this->attributes['class'];
        }
        return $notice_class;
    }
    /**
     * Get attributes as string.
     *
     * @return string
     */
    protected function getAttributesAsString()
    {
        $attribute_string = \sprintf('class="%1$s"', \esc_attr($this->getNoticeClass()));
        foreach ($this->attributes as $attribute_name => $attribute_value) {
            if ('class' !== $attribute_name) {
                $attribute_string .= \sprintf(' %1$s="%2$s"', \esc_html($attribute_name), \esc_attr($attribute_value));
            }
        }
        return $attribute_string;
    }
    private function addParagraphToContent()
    {
        if (0 === \strpos($this->noticeContent, '<p>')) {
            return \false;
        }
        if (0 === \strpos($this->noticeContent, '<div>') || 0 === \strpos($this->noticeContent, '<div ')) {
            return \false;
        }
        return \true;
    }
    /**
     * Show notice;
     */
    public function showNotice()
    {
        $this->removeAction();
        $noticeFormat = '<div %1$s>%2$s</div>';
        if ($this->addParagraphToContent()) {
            $noticeFormat = '<div %1$s><p>%2$s</p></div>';
        }
        echo \sprintf($noticeFormat, $this->getAttributesAsString(), $this->noticeContent);
    }
}
