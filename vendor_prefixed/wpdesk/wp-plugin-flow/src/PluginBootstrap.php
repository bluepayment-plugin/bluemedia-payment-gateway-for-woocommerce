<?php

namespace BmWoocommerceVendor\WPDesk\Plugin\Flow;

use BmWoocommerceVendor\WPDesk\Plugin\Flow\Initialization\InitializationFactory;
/**
 * Bootstrap plugin loading
 * - check requirements
 * - prepare plugin info
 * - delegate plugin building to the initializator
 */
final class PluginBootstrap
{
    const LIBRARY_TEXT_DOMAIN = 'bm-woocommerce';
    const PRIORITY_BEFORE_FLOW_2_5 = -50;
    /** @var string */
    private $plugin_version;
    /** @var string */
    private $plugin_name;
    /** @var string */
    private $plugin_class_name;
    /** @var string */
    private $plugin_text_domain;
    /** @var string */
    private $plugin_dir;
    /** @var string */
    private $plugin_file;
    /** @var array */
    private $requirements;
    /** @var string */
    private $product_id;
    /**
     * Factory to build strategy how initialize that plugin
     *
     * @var InitializationFactory
     */
    private $initialization_factory;
    /**
     * WPDesk_Plugin_Bootstrap constructor.
     *
     * @param string $plugin_version
     * @param string $plugin_release_timestamp
     * @param string $plugin_name
     * @param string $plugin_class_name
     * @param string $plugin_text_domain
     * @param string $plugin_dir
     * @param string $plugin_file
     * @param array $requirements
     * @param string $product_id
     * @param InitializationFactory $build_factory
     */
    public function __construct($plugin_version, $plugin_release_timestamp, $plugin_name, $plugin_class_name, $plugin_text_domain, $plugin_dir, $plugin_file, array $requirements, $product_id, \BmWoocommerceVendor\WPDesk\Plugin\Flow\Initialization\InitializationFactory $build_factory)
    {
        $this->plugin_version = $plugin_version;
        $this->plugin_name = $plugin_name;
        $this->plugin_class_name = $plugin_class_name;
        $this->plugin_text_domain = $plugin_text_domain;
        $this->plugin_dir = $plugin_dir;
        $this->plugin_file = $plugin_file;
        $this->requirements = $requirements;
        $this->product_id = $product_id;
        $this->initialization_factory = $build_factory;
    }
    /**
     * Run the plugin bootstrap
     */
    public function run()
    {
        $plugin_info = $this->get_plugin_info();
        $this->init_translations($plugin_info);
        $strategy = $this->initialization_factory->create_initialization_strategy($plugin_info);
        $requirements_checker = $this->create_requirements_checker();
        if ($requirements_checker->are_requirements_met()) {
            $strategy->run_before_init($plugin_info);
        }
        $this->add_activation_hook_for_save_activation_date();
        \add_action('plugins_loaded', static function () use($strategy, $requirements_checker, $plugin_info) {
            if ($requirements_checker->are_requirements_met()) {
                $strategy->run_init($plugin_info);
            } else {
                $requirements_checker->render_notices();
            }
        }, self::PRIORITY_BEFORE_FLOW_2_5);
    }
    /**
     * Initialize activated_plugin action.
     * Action stores plugin activation date.
     * Example option name: plugin_activation_flexible-shipping/flexible-shipping.php
     */
    private function add_activation_hook_for_save_activation_date()
    {
        \add_action('activated_plugin', static function ($plugin_file, $network_wide = \false) {
            if (!$network_wide) {
                $option_name = 'plugin_activation_' . $plugin_file;
                $activation_date = \get_option($option_name, '');
                if ('' === $activation_date) {
                    $activation_date = \current_time('mysql');
                    \update_option($option_name, $activation_date);
                }
            }
        });
    }
    /**
     * Adds text domain used in a library
     */
    private function init_translations(\BmWoocommerceVendor\WPDesk_Plugin_Info $plugin_info)
    {
        $lang_dir = 'lang';
        if (\method_exists($plugin_info, 'get_language_dir')) {
            $lang_dir = $plugin_info->get_language_dir();
        }
        \load_plugin_textdomain($plugin_info->get_text_domain(), \false, \basename($plugin_info->get_plugin_dir()) . "/{$lang_dir}/");
    }
    /**
     * Factory method creates requirement checker to run the checks
     *
     * @return \WPDesk_Requirement_Checker
     */
    private function create_requirements_checker()
    {
        /** @var \WPDesk_Requirement_Checker_Factory $requirements_checker_factory */
        $requirements_checker_factory = new \BmWoocommerceVendor\WPDesk_Basic_Requirement_Checker_Factory();
        return $requirements_checker_factory->create_from_requirement_array(__FILE__, $this->plugin_name, $this->requirements, $this->plugin_text_domain);
    }
    /**
     * Factory method creates \WPDesk_Plugin_Info to bootstrap info about plugin in one place
     *
     * TODO: move to WPDesk_Plugin_Info factory
     *
     * @return \WPDesk_Plugin_Info
     */
    private function get_plugin_info()
    {
        $plugin_info = new \BmWoocommerceVendor\WPDesk_Plugin_Info();
        $plugin_info->set_plugin_file_name(\plugin_basename($this->plugin_file));
        $plugin_info->set_plugin_name($this->plugin_name);
        $plugin_info->set_plugin_dir($this->plugin_dir);
        $plugin_info->set_class_name($this->plugin_class_name);
        $plugin_info->set_version($this->plugin_version);
        $plugin_info->set_product_id($this->product_id);
        $plugin_info->set_text_domain($this->plugin_text_domain);
        $plugin_info->set_plugin_url(\plugins_url(\dirname(\plugin_basename($this->plugin_file))));
        return $plugin_info;
    }
}
