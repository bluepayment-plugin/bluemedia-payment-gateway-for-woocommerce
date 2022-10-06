<?php

namespace Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Storage\Writable;

use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Interfaces\Post_Writable_Interface;
use Ilabs\BM_Woocommerce\Ilabs_Plugin\Event_Chain\Interfaces\Writable_Interface;

class Writable_Post_Meta implements Writable_Interface, Post_Writable_Interface {

	private $post_id;
	private $key;
	private $value;

	public function __construct(
		string $key = null,
		$value = null,
		int $post_id = null
	) {
		$this->post_id = $post_id;
		$this->key     = $key;
		$this->value   = $value;
	}


	public function write( $key = null, $value = null ) {
		if ( ! $key ) {
			$key = $this->key;
		}

		if ( ! $value ) {
			$value = $this->value;
		}

		update_post_meta( $key, $value, $this->post_id );
	}

	public function set_post_id( int $post_id ) {
		$this->post_id = $post_id;
	}

	public function get_post_id(): ?int {
		return $this->post_id;
	}

	public function set_key( string $key ): Writable_Interface {
		$this->key = $key;

		return $this;
	}

	public function set_value( $value ): Writable_Interface {
		$this->value = $value;

		return $this;
	}

	public function get_key(): string {
		return $this->key;
	}
}
