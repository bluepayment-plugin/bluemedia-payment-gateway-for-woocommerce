<?php

namespace Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Interfaces;

interface Cache_Interface {

	public function push( $value, string $key = null );

	public function set( $value, string $key = null );

	public function get( string $key = null );

	public function get_single( string $key = null );

	public function clear( string $key = null );
}
