<?php
/**
 * Plugin Name: Blue Media Woocommerce
 * Plugin URI:
 * Description: Blue Media Woocommerce
 * Product: Blue Media Woocommerce
 * Version: 4.0.0
 * Author: INSPIRE LABS
 * Author URI: https://inspirelabs.pl/
 * Text Domain: empik-woocommerce
 * Domain Path: /languages/
 *
 * Copyright 2021 INSPIRE LABS SP. Z O.O.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$plugin_version = '4.0.0';

$plugin_name        = 'Blue Media Woocommerce';
$plugin_class_name  = '\Inspire_Labs\BM_Woocommerce\Plugin';
$plugin_text_domain = 'bm-woocommerce';
$product_id         = 'bm-woocommerce';
$plugin_file        = __FILE__;
$plugin_dir         = dirname( __FILE__ );

define( 'BM_PLUGIN_URL',
	plugin_dir_url( $plugin_file )
);

$requirements = [
	'php'     => '7.2',
	'wp'      => '5.0',
	'plugins' => [
		[
			'name'      => 'woocommerce/woocommerce.php',
			'nice_name' => 'WooCommerce',
			'version'   => '4.7',
		],
	],
];

define( 'BM_WOOCOMMERCE_PLUGIN_PATH',
	plugin_dir_path( __FILE__ )
);

define( 'BM_WOOCOMMERCE_PLUGIN_BASENAME',
	plugin_basename( __FILE__ )
);

define( 'BM_WOOCOMMERCE_PLUGIN_URL',
	plugin_dir_url( __FILE__ )
);

require __DIR__
        . '/vendor_prefixed/wpdesk/wp-plugin-flow/src/plugin-init-php52.php';
