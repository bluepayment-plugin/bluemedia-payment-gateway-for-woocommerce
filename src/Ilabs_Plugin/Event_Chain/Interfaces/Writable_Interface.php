<?php

namespace Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Interfaces;

interface Writable_Interface {

	public function write( $key = null, $value = null );

	public function get_key(): string;

}