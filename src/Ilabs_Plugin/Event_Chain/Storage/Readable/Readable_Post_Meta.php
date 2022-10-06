<?php

namespace Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Storage\Readable;

use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Interfaces\Post_Readable_Interface;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Interfaces\Readable_Interface;

class Readable_Post_Meta implements Readable_Interface, Post_Readable_Interface  {

	private $post_id;
	private $key;

	public function __construct( string $key = null, int $post_id = null ) {
		$this->post_id = $post_id;
		$this->key     = $key;
	}

	public function set_post_id( int $post_id ) {
		$this->post_id = $post_id;
	}

	public function read( $key = null ) {
		if ( ! $key ) {
			$key = $this->key;
		}

		return get_post_meta( $this->post_id, $key );
	}

	public function get_post_id(): ?int {
		return $this->post_id;
	}

	public function set_key( string $key ): Readable_Interface {
		$this->key = $key;

		return $this;
	}

	public function get_key(): string {
		return $this->key;
	}
}