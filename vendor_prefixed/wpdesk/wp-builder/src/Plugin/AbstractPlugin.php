<?php

namespace BmWoocommerceVendor\WPDesk\PluginBuilder\Plugin;

/**
 * Base plugin with most basic functionalities used by every WPDesk plugin.
 *
 *
 * Known issues:
 *
 * The class name is too generic but can't be changed as it would introduce a major incompatibility for most of the plugins.
 * The $plugin_url, $docs_url and most other fields should be removed as they only litter the place but for compatibility reasons we can't do it right now.
 * Hook methods should be moved to external classes but for compatibility reasons we can't do it right now.
 */
abstract class AbstractPlugin extends \BmWoocommerceVendor\WPDesk\PluginBuilder\Plugin\SlimPlugin
{
    /**
     * Most info about plugin internals.
     *
     * @var \WPDesk_Plugin_Info
     */
    protected $plugin_info;
    /**
     * Unique string for this plugin in [a-z_]+ format.
     *
     * @var string
     */
    protected $plugin_namespace;
    /**
     * Absolute URL to the plugin dir.
     *
     * @var string
     */
    protected $plugin_url;
    /**
     * Absolute URL to the plugin docs.
     *
     * @var string
     */
    protected $docs_url;
    /**
     * Absolute URL to the plugin settings url.
     *
     * @var string
     */
    protected $settings_url;
    /**
     * Support URL.
     *
     * @var string
     */
    protected $support_url;
    /**
     * AbstractPlugin constructor.
     *
     * @param \WPDesk_Plugin_Info $plugin_info
     */
    public function __construct($plugin_info)
    {
        $this->plugin_info = $plugin_info;
        $this->plugin_namespace = \strtolower($plugin_info->get_plugin_dir());
        $this->plugin_url = $this->plugin_info->get_plugin_url();
        $this->init_base_variables();
    }
    /**
     * Initialize internal state of the plugin.
     *
     * @return void
     * @deprecated Just use __construct to initialize plugin internal state.
     *
     */
    public function init_base_variables()
    {
    }
    /**
     * Initializes plugin external state.
     *
     * The plugin internal state is initialized in the constructor and the plugin should be internally consistent after creation.
     * The external state includes hooks execution, communication with other plugins, integration with WC etc.
     *
     * @return void
     */
    public function init()
    {
        $this->hooks();
    }
    /**
     * Returns absolute path to the plugin dir.
     *
     * @return string
     */
    public function get_plugin_file_path()
    {
        return $this->plugin_info->get_plugin_file_name();
    }
    /**
     * Returns plugin text domain.
     *
     * @return string
     */
    public function get_text_domain()
    {
        return $this->plugin_info->get_text_domain();
    }
    /**
     * Returns unique string for plugin in [a-z_]+ format. Can be used as plugin id in various places like plugin slug etc.
     *
     * @return string
     */
    public function get_namespace()
    {
        return $this->plugin_namespace;
    }
    /**
     * Returns plugin absolute URL.
     *
     * @return string
     */
    public function get_plugin_url()
    {
        return \esc_url(\trailingslashit($this->plugin_url));
    }
    /**
     * Returns plugin absolute URL to dir with front end assets.
     *
     * @return string
     */
    public function get_plugin_assets_url()
    {
        return \esc_url(\trailingslashit($this->get_plugin_url() . 'assets'));
    }
    /**
     * @return $this
     * @deprecated For backward compatibility.
     *
     */
    public function get_plugin()
    {
        return $this;
    }
    /**
     * Integrate with WordPress and with other plugins using action/filter system.
     *
     * @return void
     */
    protected function hooks()
    {
        \add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
        \add_action('wp_enqueue_scripts', [$this, 'wp_enqueue_scripts']);
        \add_action('plugins_loaded', [$this, 'load_plugin_text_domain']);
        \add_filter('plugin_action_links_' . \plugin_basename($this->get_plugin_file_path()), [$this, 'links_filter']);
    }
    /**
     * Initialize plugin test domain. This is a hook function. Do not execute directly.
     *
     * @return void
     */
    public function load_plugin_text_domain()
    {
        \load_plugin_textdomain($this->get_text_domain(), \false, $this->get_namespace() . '/lang/');
    }
    /**
     * Append JS scripts in the WordPress admin panel. This is a hook function. Do not execute directly.
     *
     * @return void
     */
    public function admin_enqueue_scripts()
    {
    }
    /**
     * Append JS scripts in WordPress. This is a hook function. Do not execute directly.
     *
     * @return void
     */
    public function wp_enqueue_scripts()
    {
    }
    /**
     * Initialize plugin admin links. This is a hook function. Do not execute directly.
     *
     * @param string[] $links
     *
     * @return string[]
     */
    public function links_filter($links)
    {
        $support_link = \get_locale() === 'pl_PL' ? 'https://www.wpdesk.pl/support/' : 'https://www.wpdesk.net/support';
        if ($this->support_url) {
            $support_link = $this->support_url;
        }
        $plugin_links = ['<a target="_blank" href="' . $support_link . '">' . \__('Support', $this->get_text_domain()) . '</a>'];
        $links = \array_merge($plugin_links, $links);
        if ($this->docs_url) {
            $plugin_links = ['<a target="_blank" href="' . $this->docs_url . '">' . \__('Docs', $this->get_text_domain()) . '</a>'];
            $links = \array_merge($plugin_links, $links);
        }
        if ($this->settings_url) {
            $plugin_links = ['<a href="' . $this->settings_url . '">' . \__('Settings', $this->get_text_domain()) . '</a>'];
            $links = \array_merge($plugin_links, $links);
        }
        return $links;
    }
}
