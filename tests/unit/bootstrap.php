<?php
/**
 * PHPUnit bootstrap file
 */

require_once __DIR__ . '/../../vendor/autoload.php';

error_reporting( E_ALL );

if ( getenv( 'PLUGIN_PATH' ) !== false ) {
	define( 'PLUGIN_PATH', getenv( 'PLUGIN_PATH' ) );
} else {
	define( 'PLUGIN_PATH', __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR );
}

if ( getenv( 'ABSPATH' ) !== false ) {
	define( 'ABSPATH', getenv( 'ABSPATH' ) );
} else {
	define( 'ABSPATH', PLUGIN_PATH . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR );
}

WP_Mock::setUsePatchwork( true );
WP_Mock::bootstrap();
