<?php

namespace BmWoocommerceVendor;

if (!\interface_exists('BmWoocommerceVendor\\WPDesk_Translatable')) {
    require_once __DIR__ . '/Translatable.php';
}
if (!\interface_exists('BmWoocommerceVendor\\WPDesk_Buildable')) {
    require_once __DIR__ . '/Buildable.php';
}
if (!\interface_exists('BmWoocommerceVendor\\WPDesk_Has_Plugin_Info')) {
    require_once __DIR__ . '/Has_Plugin_Info.php';
}
/**
 * Structure with core info about plugin
 *
 * have to be compatible with PHP 5.2.x
 */
class WPDesk_Plugin_Info implements \BmWoocommerceVendor\WPDesk_Translatable, \BmWoocommerceVendor\WPDesk_Buildable, \BmWoocommerceVendor\WPDesk_Has_Plugin_Info
{
    /** @var string */
    private $plugin_file_name;
    /** @var string */
    private $plugin_dir;
    /** @var string */
    private $plugin_url;
    /** @var string */
    private $class_name;
    /** @var string */
    private $version;
    /** @var string */
    private $product_id;
    /** @var string */
    private $plugin_name;
    /** @var \DateTimeInterface */
    private $release_date;
    /** string */
    private $text_domain;
    /**
     * @return string
     */
    public function get_plugin_file_name()
    {
        return $this->plugin_file_name;
    }
    /**
     * @param string $plugin_name
     */
    public function set_plugin_file_name($plugin_name)
    {
        $this->plugin_file_name = $plugin_name;
    }
    /**
     * @return string
     */
    public function get_plugin_dir()
    {
        return $this->plugin_dir;
    }
    /**
     * @param string $plugin_dir
     */
    public function set_plugin_dir($plugin_dir)
    {
        $this->plugin_dir = $plugin_dir;
    }
    /**
     * @return string
     */
    public function get_plugin_url()
    {
        return $this->plugin_url;
    }
    /**
     * @param string $plugin_url
     */
    public function set_plugin_url($plugin_url)
    {
        $this->plugin_url = $plugin_url;
    }
    /**
     * @return string
     */
    public function get_version()
    {
        return $this->version;
    }
    /**
     * @param string $version
     */
    public function set_version($version)
    {
        $this->version = $version;
    }
    /**
     * @return string
     */
    public function get_product_id()
    {
        return $this->product_id;
    }
    /**
     * @param string $product_id
     */
    public function set_product_id($product_id)
    {
        $this->product_id = $product_id;
    }
    /**
     * @return string
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }
    /**
     * @param string $plugin_name
     */
    public function set_plugin_name($plugin_name)
    {
        $this->plugin_name = $plugin_name;
    }
    /**
     * @return DateTimeInterface
     */
    public function get_release_date()
    {
        return $this->release_date;
    }
    /**
     * @param \DateTimeInterface $release_date
     */
    public function set_release_date($release_date)
    {
        $this->release_date = $release_date;
    }
    /**
     * @return string
     */
    public function get_class_name()
    {
        return $this->class_name;
    }
    /**
     * @param string $class_name
     */
    public function set_class_name($class_name)
    {
        $this->class_name = $class_name;
    }
    /**
     * @return string
     */
    public function get_text_domain()
    {
        return $this->text_domain;
    }
    /**
     * @param $value
     */
    public function set_text_domain($value)
    {
        $this->text_domain = $value;
    }
}
