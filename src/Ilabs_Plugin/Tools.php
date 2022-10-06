<?php

namespace Ilabs\BM_Woocommerce\Ilabs_Plugin;

trait Tools {

	public function require_wp_core_file( string $path ) {
		require_once ABSPATH . $path;
	}
}