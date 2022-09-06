<?php
/**
 * Plugin Name: Blue Media Woocommerce
 * Plugin URI: https://github.com/bluepayment-plugin/bluemedia-payment-gateway-for-woocommerce
 * Description: Blue Media Woocommerce
 * Product: Blue Media Woocommerce
 * Version: 4.0.9
 * Author: iLabs LTD
 * Author URI: iLabs.dev
 * Text Domain: bm_woocommerce
 * Domain Path: /lang/
 *
 * Copyright 2022 iLabs LTD
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

$plugin_file = __FILE__;
$plugin_dir  = dirname( __FILE__ );

define( 'BM_PLUGIN_URL',
	plugin_dir_url( $plugin_file )
);

add_action( 'plugins_loaded', function () {
	load_plugin_textdomain( 'bm-woocommerce', false,
		basename( dirname( __FILE__ ) ) . '/lang/' );
} );

define( 'BM_WOOCOMMERCE_PLUGIN_PATH',
	plugin_dir_path( __FILE__ )
);

define( 'BM_WOOCOMMERCE_PLUGIN_BASENAME',
	plugin_basename( __FILE__ )
);

define( 'BM_WOOCOMMERCE_PLUGIN_URL',
	plugin_dir_url( __FILE__ )
);

require_once __DIR__ . '/vendor/autoload.php';

( new \Inspire_Labs\BM_Woocommerce\Plugin() )->init();
