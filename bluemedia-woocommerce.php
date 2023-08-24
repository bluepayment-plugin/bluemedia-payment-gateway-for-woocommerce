<?php
declare( strict_types=1 );
/**
 * Plugin Name: Blue Media
 * Plugin URI: https://wordpress.org/plugins/platnosci-online-blue-media
 * Description: Blue Media Woocommerce
 * Product: Blue Media Woocommerce
 * Version: 4.1.26
 * Tested up to: 6.2.2
 * Requires PHP: 7.3
 * Author: iLabs LTD
 * Author URI: iLabs.dev
 * Text Domain: bm-woocommerce
 * Domain Path: /lang/
 *
 * Copyright 2023 iLabs LTD
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
}


require_once __DIR__ . '/compatibility.php';

if ( blue_media_system_check() ) {
	require_once __DIR__ . '/vendor/autoload.php';
	require_once 'dependencies.php';

	function blue_media(): Ilabs\BM_Woocommerce\Plugin {
		return new Ilabs\BM_Woocommerce\Plugin();
	}

	$config = [
		'__FILE__'    => __FILE__,
		'slug'        => 'bm_woocommerce',
		'lang_dir'    => 'lang',
		'text_domain' => 'bm-woocommerce',
	];

	blue_media()->execute( $config );
}



