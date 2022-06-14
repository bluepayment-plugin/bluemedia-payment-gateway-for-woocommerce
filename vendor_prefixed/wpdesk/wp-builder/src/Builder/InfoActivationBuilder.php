<?php

namespace BmWoocommerceVendor\WPDesk\PluginBuilder\Builder;

use BmWoocommerceVendor\WPDesk\PluginBuilder\Plugin\AbstractPlugin;
use BmWoocommerceVendor\WPDesk\PluginBuilder\Plugin\ActivationAware;
use BmWoocommerceVendor\WPDesk\PluginBuilder\Storage\PluginStorage;
/**
 * Builder that have info about activations
 *
 * Warning: We can't extend InfoBuilder.php as some old plugins(without wp-flow) will load the old version od InfoBuilder class that have private plugin property.
 *
 * @package WPDesk\PluginBuilder\Builder
 */
class InfoActivationBuilder extends \BmWoocommerceVendor\WPDesk\PluginBuilder\Builder\AbstractBuilder
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
    /**
     * @var bool
     */
    private $is_active;
    /**
     * @param \WPDesk_Buildable $info
     * @param bool $is_active
     */
    public function __construct(\BmWoocommerceVendor\WPDesk_Buildable $info, $is_active)
    {
        $this->info = $info;
        $this->storage_id = $info->get_class_name();
        $this->is_active = $is_active;
    }
    /**
     * Builds instance of plugin
     */
    public function build_plugin()
    {
        $class_name = \apply_filters(self::FILTER_PLUGIN_CLASS, $this->info->get_class_name());
        /** @var AbstractPlugin $plugin */
        $this->plugin = new $class_name($this->info);
        if ($this->plugin instanceof \BmWoocommerceVendor\WPDesk\PluginBuilder\Plugin\ActivationAware && $this->is_active) {
            $this->plugin->set_active();
        }
        return $this->plugin;
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
