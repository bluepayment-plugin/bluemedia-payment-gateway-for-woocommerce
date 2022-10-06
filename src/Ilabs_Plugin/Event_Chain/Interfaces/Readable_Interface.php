<?php

namespace Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Interfaces;

interface Readable_Interface {

	public function read( string $key = null );

	public function get_key(): string;
}