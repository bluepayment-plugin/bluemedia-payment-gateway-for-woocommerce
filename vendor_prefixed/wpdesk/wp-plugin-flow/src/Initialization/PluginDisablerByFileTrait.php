<?php

namespace BmWoocommerceVendor\WPDesk\Plugin\Flow\Initialization;

/**
 * Can disable shared plugin before it's loaded using plugin filename
 */
class PluginDisablerByFileTrait
{
    /** @var string */
    private $plugin_file;
    /**
     * @param string $plugin_file
     */
    public function __construct($plugin_file)
    {
        $this->plugin_file = $plugin_file;
    }
    /**
     * @return void
     */
    public function disable()
    {
        /**
         * @param WPDesk_Loader[] $loaders
         *
         * @return array
         */
        $false_for_helper = function ($loaders) {
            return \array_filter($loaders, function ($loader) {
                try {
                    // BIG HACK TO GET PRIVATE PROPERTY
                    $reflection = new \ReflectionClass($loader);
                    $property = $reflection->getProperty('loader_info');
                    $property->setAccessible(\true);
                    /** @var WPDesk_Composer_Loader_Info $inner_info */
                    $inner_info = $property->getValue($loader);
                    $plugin_info = $inner_info->get_plugin_info();
                    return \basename($plugin_info->get_plugin_file_name()) !== \basename($this->plugin_file);
                } catch (\Exception $e) {
                    return \true;
                }
            });
        };
        \add_filter('wp_autoloader_loader_loaders_to_load', $false_for_helper);
        \add_filter('wp_autoloader_loader_loaders_to_create', $false_for_helper);
    }
}
