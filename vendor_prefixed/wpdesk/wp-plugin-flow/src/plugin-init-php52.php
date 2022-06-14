<?php

namespace BmWoocommerceVendor;

/**
 * @var string                                                       $plugin_version
 * @var string                                                       $plugin_name
 * @var string                                                       $plugin_class_name
 * @var string                                                       $plugin_text_domain
 * @var string                                                       $plugin_dir
 * @var string                                                       $plugin_file
 * @var array                                                        $requirements
 * @var string                                                       $product_id
 * @var WPDesk\Plugin\Flow\Initialization\InitializationFactory|void $plugin_init_factory
 */
if (!\defined('ABSPATH')) {
    die;
}
// Code in PHP >= 5.3 but understandable by older parsers
if (\PHP_VERSION_ID > 50300) {
    require_once $plugin_dir . '/vendor/autoload.php';
    if (!isset($plugin_init_factory)) {
        $plugin_init_factory = new \BmWoocommerceVendor\WPDesk\Plugin\Flow\Initialization\Simple\SimpleFactory();
    }
    $bootstrap = new \BmWoocommerceVendor\WPDesk\Plugin\Flow\PluginBootstrap(
        $plugin_version,
        null,
        // deprecated
        $plugin_name,
        $plugin_class_name,
        $plugin_text_domain,
        $plugin_dir,
        $plugin_file,
        $requirements,
        $product_id,
        $plugin_init_factory
    );
    $bootstrap->run();
    // all optional vars must be cleared
    unset($plugin_init_factory);
} else {
    /** @noinspection PhpDeprecationInspection */
    $php52_function = \create_function('', 'echo sprintf( __("<p><strong style=\'color: red;\'>PHP version is older than 5.3 so no WP Desk plugins will work. Please contact your host and ask them to upgrade. </strong></p>", \'wp-plugin-flow\') );');
    \add_action('admin_notices', $php52_function);
}
