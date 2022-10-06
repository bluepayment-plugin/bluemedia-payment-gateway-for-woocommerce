<?php

namespace Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Storage\Writable;

use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Interfaces\Writable_Interface;

class Option implements Writable_Interface {

	public function write( $key = null, $value = null ) {
		update_option( $key, $value );
	}

	public function set_key( string $key ): Writable_Interface {
		// TODO: Implement set_key() method.
	}

	public function set_value( $value ): Writable_Interface {
		// TODO: Implement set_value() method.
	}

	public function get_key(): string {
		// TODO: Implement get_key() method.
	}


}
