<?php

namespace BmWoocommerceVendor\WPDesk\PluginBuilder\Builder;

use BmWoocommerceVendor\WPDesk\PluginBuilder\Plugin\AbstractPlugin;
use BmWoocommerceVendor\WPDesk\PluginBuilder\Storage\PluginStorage;
/**
 * @deprecated Should not be used as some old plugins are using it and we can't touch this.
 *
 * @package WPDesk\PluginBuilder\Builder
 */
class InfoBuilder extends \BmWoocommerceVendor\WPDesk\PluginBuilder\Builder\AbstractBuilder
{
    const FILTER_PLUGIN_CLASS = 'wp_builder_plugin_class';
    const HOOK_BEFORE_PLUGIN_INIT = 'wp_builder_before_plugin_init';
    const HOOK_AFTER_PLUGIN_INIT = 'wp_builder_before_init';
    /** @var AbstractPlugin */
    private $plugin;
    /** @var \WPDesk_Buildable */
    private $info;
    /** @var string */
    protected $storage_id;
    public function __construct(\BmWoocommerceVendor\WPDesk_Buildable $info)
    {
        $this->info = $info;
        $this->storage_id = $info->get_class_name();
    }
    /**
     * Builds instance of plugin
     */
    public function build_plugin()
    {
        $class_name = \apply_filters(self::FILTER_PLUGIN_CLASS, $this->info->get_class_name());
        /** @var AbstractPlugin $plugin */
        $this->plugin = new $class_name($this->info);
    }
    public function store_plugin(\BmWoocommerceVendor\WPDesk\PluginBuilder\Storage\PluginStorage $storage)
    {
        $storage->add_to_storage($this->storage_id, $this->plugin);
    }
    public function init_plugin()
    {
        \do_action(self::HOOK_BEFORE_PLUGIN_INIT, $this->plugin);
        $this->plugin->init();
        \do_action(self::HOOK_AFTER_PLUGIN_INIT, $this->plugin);
    }
    /**
     * @return AbstractPlugin
     */
    public function get_plugin()
    {
        return $this->plugin;
    }
}
