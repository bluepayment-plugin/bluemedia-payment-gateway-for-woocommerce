<?php

namespace BmWoocommerceVendor;

/**
 * @var string $plugin_version
 * @var string $plugin_name
 * @var string $plugin_class_name
 * @var string $plugin_text_domain
 * @var string $plugin_dir
 * @var string $plugin_file
 * @var array  $requirements
 * @var string $product_id
 */
if (!\defined('ABSPATH')) {
    die;
}
// Code in PHP >= 5.3 but understandable by older parsers
if (\PHP_VERSION_ID > 50300) {
    require_once $plugin_dir . '/vendor/autoload.php';
    $plugin_init_factory = new \BmWoocommerceVendor\WPDesk\Plugin\Flow\Initialization\Simple\SimpleFactory(\true);
}
require \dirname(__FILE__) . '/plugin-init-php52.php';
