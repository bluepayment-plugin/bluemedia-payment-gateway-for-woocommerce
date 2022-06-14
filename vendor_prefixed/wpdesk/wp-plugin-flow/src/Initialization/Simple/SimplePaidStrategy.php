<?php

namespace BmWoocommerceVendor\WPDesk\Plugin\Flow\Initialization\Simple;

use BmWoocommerceVendor\WPDesk\Helper\HelperRemover;
use BmWoocommerceVendor\WPDesk\Helper\PrefixedHelperAsLibrary;
use BmWoocommerceVendor\WPDesk\License\PluginRegistrator;
use BmWoocommerceVendor\WPDesk\Plugin\Flow\Initialization\ActivationTrait;
use BmWoocommerceVendor\WPDesk\Plugin\Flow\Initialization\BuilderTrait;
use BmWoocommerceVendor\WPDesk\Plugin\Flow\Initialization\PluginDisablerByFileTrait;
use BmWoocommerceVendor\WPDesk\Plugin\Flow\Initialization\InitializationStrategy;
use BmWoocommerceVendor\WPDesk\PluginBuilder\Plugin\ActivationAware;
use BmWoocommerceVendor\WPDesk\PluginBuilder\Plugin\SlimPlugin;
/**
 * Initialize standard paid plugin
 * - register to helper
 * - initialize helper
 * - build with info about plugin active flag
 */
class SimplePaidStrategy implements \BmWoocommerceVendor\WPDesk\Plugin\Flow\Initialization\InitializationStrategy
{
    use HelperInstanceAsFilterTrait;
    use TrackerInstanceAsFilterTrait;
    use BuilderTrait;
    /** @var \WPDesk_Plugin_Info */
    private $plugin_info;
    /** @var SlimPlugin */
    private $plugin;
    public function __construct(\BmWoocommerceVendor\WPDesk_Plugin_Info $plugin_info)
    {
        $this->plugin_info = $plugin_info;
    }
    /**
     * Run tasks that prepares plugin to work. Have to run before plugin loaded.
     *
     * @param \WPDesk_Plugin_Info $plugin_info
     *
     * @return SlimPlugin
     */
    public function run_before_init(\BmWoocommerceVendor\WPDesk_Plugin_Info $plugin_info)
    {
        $this->plugin = $this->build_plugin($plugin_info);
        $this->init_register_hooks($plugin_info, $this->plugin);
    }
    /**
     * Run task that integrates plugin with other dependencies. Can be run in plugins_loaded.
     *
     * @param \WPDesk_Plugin_Info $plugin_info
     *
     * @return SlimPlugin
     */
    public function run_init(\BmWoocommerceVendor\WPDesk_Plugin_Info $plugin_info)
    {
        if (!$this->plugin) {
            $this->plugin = $this->build_plugin($plugin_info);
        }
        $this->prepare_tracker_action();
        $registrator = $this->register_plugin();
        \add_action('plugins_loaded', function () use($registrator) {
            $this->init_helper();
            $is_plugin_subscription_active = $registrator instanceof \BmWoocommerceVendor\WPDesk\License\PluginRegistrator && $registrator->is_active();
            if ($this->plugin instanceof \BmWoocommerceVendor\WPDesk\PluginBuilder\Plugin\ActivationAware && $is_plugin_subscription_active) {
                $this->plugin->set_active();
            }
            $this->store_plugin($this->plugin);
            $this->init_plugin($this->plugin);
        }, $priority_before_flow_2_5_after_2_6 = -45);
        return $this->plugin;
    }
    /**
     * Register plugin for subscriptions and updates
     *
     * @return PluginRegistrator
     *
     * @see init_helper note
     *
     */
    private function register_plugin()
    {
        if (\apply_filters('wpdesk_can_register_plugin', \true, $this->plugin_info)) {
            $registrator = new \BmWoocommerceVendor\WPDesk\License\PluginRegistrator($this->plugin_info);
            $registrator->add_plugin_to_installed_plugins();
            return $registrator;
        }
    }
    /**
     * Helper is a component that gives:
     * - activation interface
     * - automatic updates
     * - logs
     * - some other feats
     *
     * NOTE:
     *
     * It's possible for this method to not found classes embedded here.
     * OTHER plugin in unlikely scenario that THIS plugin is disabled
     * can use this class and do not have this library dependencies as
     * these are loaded using composer.
     *
     * @return PrefixedHelperAsLibrary|null
     */
    private function init_helper()
    {
        $this->prevent_older_helpers();
        $this->prepare_helper_action();
        return $this->get_helper_instance();
    }
    /**
     * Try to disable all other types of helpers
     */
    private function prevent_older_helpers()
    {
        if (\apply_filters('wpdesk_can_hack_shared_helper', \true, $this->plugin_info)) {
            // hack to ensure that the class is loaded so other helpers are disabled
            \class_exists(\WPDesk\Helper\HelperAsLibrary::class, \true);
        }
        if (\apply_filters('wpdesk_can_supress_original_helper', \true, $this->plugin_info)) {
            $this->try_suppress_original_helper_load();
            // start supression only once. Prevent doing it again
            \add_filter('wpdesk_can_supress_original_helper', function () {
                return \false;
            });
        }
        if (\apply_filters('wpdesk_can_remove_old_helper_hooks', \true, $this->plugin_info)) {
            (new \BmWoocommerceVendor\WPDesk\Helper\HelperRemover())->hooks();
        }
    }
    /**
     * Tries to prevent original Helper from loading
     */
    private function try_suppress_original_helper_load()
    {
        (new \BmWoocommerceVendor\WPDesk\Plugin\Flow\Initialization\PluginDisablerByFileTrait('wpdesk-helper/wpdesk-helper.php'))->disable();
    }
}
