<?php

namespace BmWoocommerceVendor\WPDesk\PluginBuilder\Plugin;

/**
 * @deprecated Use wpdesk/wp-view
 *
 * @package WPDesk\PluginBuilder\Plugin
 */
trait TemplateLoad
{
    /**
     * Plugin path.
     *
     * @var string
     */
    protected $plugin_path;
    /**
     * Template path.
     *
     * @var string
     */
    protected $template_path;
    /**
     * Init base variables for plugin
     */
    public function init_template_base_variables()
    {
        $this->plugin_path = $this->plugin_info->get_plugin_dir();
        $this->template_path = $this->plugin_info->get_text_domain();
    }
    /**
     * Renders end returns selected template
     *
     * @param string $name Name of the template.
     * @param string $path Additional inner path to the template.
     * @param array  $args args Accessible from template.
     *
     * @return string
     */
    public function load_template($name, $path = '', $args = array())
    {
        $plugin_template_path = \trailingslashit($this->plugin_path) . 'templates/';
        // Look within passed path within the theme - this is priority.
        $template = \locate_template(array(\trailingslashit($this->get_template_path()) . \trailingslashit($path) . $name . '.php'));
        if (!$template) {
            $template = $plugin_template_path . \trailingslashit($path) . $name . '.php';
        }
        \extract($args);
        \ob_start();
        include $template;
        return \ob_get_clean();
    }
    /**
     * Get template path.
     *
     * @return string
     */
    public function get_template_path()
    {
        return \trailingslashit($this->template_path);
    }
}
