<?php

namespace Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Storage\Readable;

use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Interfaces\Readable_Interface;

class Option implements Readable_Interface {

	public function read( $key = null ) {
		return get_option($key);
	}

	public function set_key( string $key ): Readable_Interface {
		// TODO: Implement set_key() method.
	}

	public function get_key(): string {
		// TODO: Implement get_key() method.
	}


}
